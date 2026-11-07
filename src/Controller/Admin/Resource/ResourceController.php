<?php

declare(strict_types=1);

namespace App\Controller\Admin\Resource;

use App\Controller\Admin\AdminController;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\UploadException;
use App\Model\Repository\ResourceRepository;

/**
 * Liste et édition d'un type de contenu du back-office (design/pages/admin-liste.md, admin-edition.md).
 *
 * Chaque type (projets, certifications…) hérite de cette classe et déclare seulement ses champs,
 * ses libellés et son repository ; la liste, la recherche, le filtre de statut, le réordonnancement,
 * le formulaire FR / EN, la validation (ResourceForm), les fichiers et la suppression sont gérés ici.
 */
abstract class ResourceController extends AdminController
{
    public const STATUSES = ['published', 'draft'];

    public function __construct(
        protected readonly Request $request,
        protected readonly Session $session,
        protected readonly ResourceForm $form,
    ) {
    }

    abstract protected function repository(): ResourceRepository;

    /** @return list<Field> */
    abstract protected function fields(): array;

    /** Chemin de base, ex. '/admin/projets'. */
    abstract public static function basePath(): string;

    /** Nom de route de base, ex. 'admin.projects'. */
    abstract public static function routeName(): string;

    /**
     * Libellés : title (liste), back (retour), delete (lien), empty (liste vide),
     * newTitle (titre d'un nouveau contenu), saved, deleted.
     *
     * @return array<string, string>
     */
    abstract protected function labels(): array;

    /**
     * Libellés standard d'un type, lus dans lang/fr.php → admin.types.{type}.
     *
     * @return array<string, string>
     */
    protected function typeLabels(string $type): array
    {
        $key = 'admin.types.' . $type . '.';

        return [
            'title' => t($key . 'title'),
            'back' => '← ' . t($key . 'title'),
            'delete' => t($key . 'delete'),
            'empty' => t($key . 'empty'),
            'newTitle' => t($key . 'new'),
            'saved' => t('admin.form.saved'),
            'deleted' => t('admin.form.deleted'),
        ];
    }

    protected function hasStatus(): bool
    {
        return true;
    }

    protected function sortable(): bool
    {
        return true;
    }

    /** Adresse publique pour « Prévisualiser », ou null. @param array<string, mixed> $values */
    protected function previewUrl(array $values): ?string
    {
        return null;
    }

    /** Valeurs d'un nouveau contenu. @return array<string, mixed> */
    protected function defaults(): array
    {
        return ['status' => 'draft', 'translations' => ['fr' => [], 'en' => []]];
    }

    /** Contrôles propres au type. @param array<string, mixed> $values @return array<string, string> */
    protected function extraErrors(array $values, ?int $id): array
    {
        return [];
    }

    /** Déclare les routes d'un type : liste, création, édition, suppression, ordre. */
    public static function routes(Router $router): void
    {
        $base = static::basePath();
        $name = static::routeName();
        $router->get($base, [static::class, 'index'], $name);
        $router->get($base . '/nouveau', [static::class, 'create'], $name . '.create');
        $router->post($base, [static::class, 'store'], $name . '.store');
        $router->post($base . '/ordre', [static::class, 'reorder'], $name . '.reorder');
        $router->get($base . '/{id:\d+}', [static::class, 'edit'], $name . '.edit');
        $router->post($base . '/{id:\d+}', [static::class, 'update'], $name . '.update');
        $router->post($base . '/{id:\d+}/supprimer', [static::class, 'destroy'], $name . '.delete');
    }

    // ── Actions ─────────────────────────────────────────────

    /** GET liste, avec ?q= (recherche) et ?statut=publie|brouillon */
    public function index(): Response
    {
        $status = match ($this->request->query('statut')) {
            'publie' => 'published',
            'brouillon' => 'draft',
            default => null,
        };
        $search = $this->request->query('q');

        return $this->render('admin/resource/index', [
            'title' => $this->labels()['title'],
            'labels' => $this->labels(),
            'rows' => $this->repository()->adminList($search, $status),
            'search' => $search ?? '',
            'status' => $status,
            'hasStatus' => $this->hasStatus(),
            'sortable' => $this->sortable(),
            'base' => static::basePath(),
        ]);
    }

    public function create(): Response
    {
        return $this->form(null, $this->defaults(), []);
    }

    public function edit(string $id): Response
    {
        $values = $this->repository()->formValues((int) $id) ?? throw new HttpException(404);

        return $this->form((int) $id, $values, []);
    }

    public function store(): Response
    {
        return $this->save(null);
    }

    public function update(string $id): Response
    {
        if ($this->repository()->formValues((int) $id) === null) {
            throw new HttpException(404);
        }

        return $this->save((int) $id);
    }

    public function destroy(string $id): Response
    {
        foreach ($this->repository()->delete((int) $id) as $fileId) {
            $this->form->deleteFile($fileId);
        }
        $this->session->flash('notice', $this->labels()['deleted']);

        return $this->redirect(static::basePath());
    }

    /** POST …/ordre : ids[] dans le nouvel ordre (glisser-déposer de la liste). */
    public function reorder(): Response
    {
        $this->repository()->reorder(array_map('intval', $this->request->inputList('ids')));

        return new Response('', 204);
    }

    // ── Formulaire ──────────────────────────────────────────

    private function save(?int $id): Response
    {
        $previous = $id !== null ? ($this->repository()->formValues($id) ?? []) : [];
        [$values, $errors] = $this->form->read($this->fields());
        $values = $this->withSlugAndStatus($values, $errors, $id);
        $errors = [...$errors, ...$this->extraErrors($values, $id)];

        if ($errors === []) {
            try {
                $values = $this->form->storeFiles($this->fields(), $values, $previous);
            } catch (UploadException $e) {
                $errors['_files'] = t($e->getMessage());
            }
        }
        if ($errors !== []) {
            return $this->form($id, [...$previous, ...$values], $errors, 422);
        }

        $savedId = $this->repository()->save($id, $values);
        $this->form->deleteReplacedFiles($this->fields(), $previous, $values);
        $this->session->flash('notice', $this->labels()['saved']);

        return $this->redirect(static::basePath() . '/' . $savedId);
    }

    /**
     * Slug déduit du titre français s'il est vide, unique, en minuscules et tirets ; statut connu.
     *
     * @param array<string, mixed>  $values
     * @param array<string, string> $errors
     * @return array<string, mixed>
     */
    private function withSlugAndStatus(array $values, array &$errors, ?int $id): array
    {
        if ($this->hasField('slug')) {
            $slug = (string) ($values['slug'] ?? '');
            if ($slug === '') {
                $slug = slugify((string) ($values['translations']['fr'][$this->titleField()] ?? ''));
                $values['slug'] = $slug;
            }
            if ($slug !== '' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                $errors['slug'] = ResourceForm::message('slug');
            } elseif ($slug !== '' && $this->repository()->slugTaken($slug, $id)) {
                $errors['slug'] = ResourceForm::message('slugTaken');
            }
        }

        if ($this->hasStatus()) {
            $status = (string) $this->request->input('status');
            $values['status'] = in_array($status, self::STATUSES, true) ? $status : 'draft';
        }

        return $values;
    }

    /**
     * @param array<string, mixed>  $values
     * @param array<string, string> $errors
     */
    private function form(?int $id, array $values, array $errors, int $status = 200): Response
    {
        $title = (string) ($values['translations']['fr'][$this->titleField()] ?? '');

        return $this->render('admin/resource/form', [
            'title' => $title !== '' ? $title : $this->labels()['newTitle'],
            'labels' => $this->labels(),
            'id' => $id,
            'values' => $values,
            'errors' => $errors,
            'fields' => $this->fields(),
            'hasStatus' => $this->hasStatus(),
            'action' => $id === null ? static::basePath() : static::basePath() . '/' . $id,
            'deleteAction' => $id === null ? null : static::basePath() . '/' . $id . '/supprimer',
            'base' => static::basePath(),
            'previewUrl' => $id !== null ? $this->previewUrl($values) : null,
            'incomplete' => ResourceForm::incompleteLangs($this->fields(), $values),
        ], status: $status);
    }

    /** Premier champ traduit : sert de titre. */
    private function titleField(): string
    {
        foreach ($this->fields() as $field) {
            if ($field->translated) {
                return $field->name;
            }
        }

        return 'title';
    }

    private function hasField(string $name): bool
    {
        foreach ($this->fields() as $field) {
            if ($field->name === $name) {
                return true;
            }
        }

        return false;
    }
}
