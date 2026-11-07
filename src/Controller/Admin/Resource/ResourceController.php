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
use App\Core\Uploader;
use App\Model\Repository\ResourceRepository;
use App\Model\Repository\TranslatableRepository;

/**
 * Liste et édition d'un type de contenu du back-office (design/pages/admin-liste.md, admin-edition.md).
 *
 * Chaque type (projets, certifications…) hérite de cette classe et déclare seulement ses champs,
 * ses libellés et son repository ; la liste, la recherche, le filtre de statut, le réordonnancement,
 * le formulaire FR / EN, la validation, l'envoi de fichiers et la suppression sont gérés ici.
 */
abstract class ResourceController extends AdminController
{
    public const STATUSES = ['published', 'draft'];

    public function __construct(
        protected readonly Request $request,
        protected readonly Session $session,
        protected readonly Uploader $uploader,
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
     * Libellés : title (liste), new (bouton), back (retour), delete (lien), empty (liste vide),
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
            'routeName' => static::routeName(),
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
            $this->deleteFile($fileId);
        }
        $this->session->flash('notice', $this->labels()['deleted']);

        return $this->redirect(static::basePath());
    }

    /** POST …/ordre : ids[] dans le nouvel ordre (glisser-déposer de la liste). */
    public function reorder(): Response
    {
        $ids = array_map('intval', $this->request->inputList('ids'));
        $this->repository()->reorder($ids);

        return new Response('', 204);
    }

    // ── Formulaire ──────────────────────────────────────────

    private function save(?int $id): Response
    {
        $previous = $id !== null ? ($this->repository()->formValues($id) ?? []) : [];
        [$values, $errors] = $this->read($previous, $id);

        if ($errors === []) {
            try {
                $values = $this->storeFiles($values, $previous);
            } catch (UploadException $e) {
                $errors['_files'] = t($e->getMessage());
            }
        }
        if ($errors !== []) {
            return $this->form($id, [...$previous, ...$values], $errors, 422);
        }

        $savedId = $this->repository()->save($id, $values);
        $this->deleteReplacedFiles($previous, $values);
        $this->session->flash('notice', $this->labels()['saved']);

        return $this->redirect(static::basePath() . '/' . $savedId);
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
            'incomplete' => $this->incompleteLangs($values),
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

    /**
     * Langues dont un champ traduit obligatoire est vide (point rouge sur l'onglet).
     *
     * @param array<string, mixed> $values
     * @return list<string>
     */
    private function incompleteLangs(array $values): array
    {
        $incomplete = [];
        foreach (TranslatableRepository::LANGS as $lang) {
            foreach ($this->fields() as $field) {
                if ($field->translated && $field->required && trim((string) ($values['translations'][$lang][$field->name] ?? '')) === '') {
                    $incomplete[] = $lang;
                    break;
                }
            }
        }

        return $incomplete;
    }

    /**
     * Lit et valide le formulaire envoyé.
     *
     * @param array<string, mixed> $previous valeurs actuelles (fichiers déjà envoyés)
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    private function read(array $previous, ?int $id): array
    {
        $values = ['translations' => array_fill_keys(TranslatableRepository::LANGS, [])];
        $errors = [];
        $message = fn (string $key, array $replace = []) => t('admin.form.errors.' . $key, $replace);

        foreach ($this->fields() as $field) {
            if ($field->translated) {
                foreach (TranslatableRepository::LANGS as $lang) {
                    $value = $this->request->nested('tr', $lang, $field->name) ?? '';
                    $values['translations'][$lang][$field->name] = $value;
                    $key = "tr.{$lang}.{$field->name}";
                    // Seul le français est obligatoire : l'anglais vide est signalé « incomplet » et remplacé par le français sur le site.
                    if ($field->required && $lang === 'fr' && $value === '') {
                        $errors[$key] = $message('required');
                    } elseif ($field->max !== null && mb_strlen($value) > $field->max) {
                        $errors[$key] = $message('max', ['n' => $field->max]);
                    }
                }
                continue;
            }

            if ($field->isFile()) {
                continue; // traités par storeFiles(), après validation
            }

            [$value, $error] = $this->readField($field);
            $values[$field->name] = $value;
            if ($error !== null) {
                $errors[$field->name] = $message($error, ['n' => (string) $field->max]);
            }
        }

        // Slug : déduit du titre français s'il est laissé vide, unique, en minuscules et tirets.
        if ($this->hasField('slug')) {
            $slug = (string) ($values['slug'] ?? '');
            if ($slug === '') {
                $slug = slugify((string) ($values['translations']['fr'][$this->titleField()] ?? ''));
                $values['slug'] = $slug;
            }
            if ($slug !== '' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                $errors['slug'] = $message('slug');
            } elseif ($slug !== '' && $this->repository()->slugTaken($slug, $id)) {
                $errors['slug'] = $message('slugTaken');
            }
        }

        if ($this->hasStatus()) {
            $status = (string) $this->request->input('status');
            $values['status'] = in_array($status, self::STATUSES, true) ? $status : 'draft';
        }

        return [$values, [...$errors, ...$this->extraErrors($values, $id)]];
    }

    /** @return array{0: mixed, 1: ?string} valeur lue et clé d'erreur éventuelle */
    private function readField(Field $field): array
    {
        if ($field->type === 'toggle') {
            return [$this->request->input($field->name) === '1', null];
        }
        if ($field->type === 'tags') {
            // Technos déjà ajoutées (puces) + saisie libre séparée par des virgules (sans JavaScript).
            $tags = $this->request->inputList($field->name);
            foreach (explode(',', (string) $this->request->input($field->name . '_new')) as $tag) {
                $tags[] = trim($tag);
            }
            $tags = array_values(array_unique(array_filter($tags, fn ($t) => $t !== '' && mb_strlen($t) <= 40)));

            return [$tags, null];
        }

        $value = (string) $this->request->input($field->name);
        if ($value === '') {
            // Texte vide = chaîne vide (colonnes NOT NULL DEFAULT '') ; autres types vides = NULL.
            $empty = in_array($field->type, ['text', 'textarea', 'markdown'], true) ? '' : null;

            return [$empty, $field->required ? 'required' : null];
        }
        if ($field->max !== null && mb_strlen($value) > $field->max) {
            return [$value, 'max'];
        }

        return match ($field->type) {
            'url' => [$value, preg_match('#^https?://#i', $value) && filter_var($value, FILTER_VALIDATE_URL) ? null : 'url'],
            'email' => [$value, filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'email'],
            'number' => preg_match('/^-?\d+$/', $value) ? [(int) $value, null] : [$value, 'number'],
            'date' => [$value, self::validDate($value, 'Y-m-d') ? null : 'date'],
            'month' => self::validDate($value . '-01', 'Y-m-d') ? [$value . '-01', null] : [$value, 'date'],
            'select' => [$value, array_key_exists($value, $field->options) ? null : 'select'],
            default => [$value, null],
        };
    }

    private static function validDate(string $value, string $format): bool
    {
        $date = \DateTimeImmutable::createFromFormat('!' . $format, $value);

        return $date !== false && $date->format($format) === $value;
    }

    /**
     * Enregistre les fichiers envoyés et garde ceux qui ne changent pas.
     *
     * @param array<string, mixed> $values
     * @param array<string, mixed> $previous
     * @return array<string, mixed>
     */
    private function storeFiles(array $values, array $previous): array
    {
        foreach ($this->fields() as $field) {
            if (!$field->isFile()) {
                continue;
            }
            $kind = $field->type === 'pdf' ? 'pdf' : 'image';

            if ($field->type === 'gallery') {
                $keep = array_map('intval', $this->request->nestedList('keep', $field->name));
                $current = array_map('intval', (array) ($previous[$field->name] ?? []));
                $ids = array_values(array_intersect($current, $keep));
                foreach ($this->request->files($field->name) as $upload) {
                    $ids[] = $this->uploader->store($upload, 'image')->id;
                }
                $values[$field->name] = $ids;
                continue;
            }

            $upload = $this->request->file($field->name);
            if ($upload !== null) {
                $values[$field->name] = $this->uploader->store($upload, $kind)->id;
            } elseif ($this->request->nested('remove', $field->name) === '1') {
                $values[$field->name] = null;
            } else {
                $values[$field->name] = $previous[$field->name] ?? null;
            }
        }

        return $values;
    }

    /**
     * Supprime du disque les fichiers remplacés ou retirés.
     *
     * @param array<string, mixed> $previous
     * @param array<string, mixed> $values
     */
    private function deleteReplacedFiles(array $previous, array $values): void
    {
        foreach ($this->fields() as $field) {
            if (!$field->isFile()) {
                continue;
            }
            $before = array_map('intval', array_filter((array) ($previous[$field->name] ?? [])));
            $after = array_map('intval', array_filter((array) ($values[$field->name] ?? [])));
            foreach (array_diff($before, $after) as $fileId) {
                $this->deleteFile($fileId);
            }
        }
    }

    private function deleteFile(int $fileId): void
    {
        $file = \App\Core\App::get(\App\Model\Repository\FileRepository::class)->find($fileId);
        if ($file !== null) {
            $this->uploader->delete($file);
        }
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
