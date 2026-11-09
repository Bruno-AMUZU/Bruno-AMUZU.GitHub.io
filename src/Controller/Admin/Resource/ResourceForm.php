<?php

declare(strict_types=1);

namespace App\Controller\Admin\Resource;

use App\Core\Request;
use App\Core\Uploader;
use App\Model\Repository\FileRepository;
use App\Model\Repository\TranslatableRepository;

/**
 * Lecture, validation et fichiers d'un formulaire du back-office décrit par une liste de Field.
 * Utilisé par les types de contenu (ResourceController) et par la page Réglages.
 */
final class ResourceForm
{
    public function __construct(
        private readonly Request $request,
        private readonly Uploader $uploader,
        private readonly FileRepository $files,
    ) {
    }

    /**
     * Lit et valide le formulaire envoyé (hors fichiers, enregistrés seulement si tout est valide).
     *
     * @param list<Field> $fields
     * @return array{0: array<string, mixed>, 1: array<string, string>} valeurs et erreurs (champ → message)
     */
    public function read(array $fields): array
    {
        $values = ['translations' => array_fill_keys(TranslatableRepository::LANGS, [])];
        $errors = [];

        foreach ($fields as $field) {
            if ($field->translated) {
                foreach (TranslatableRepository::LANGS as $lang) {
                    $value = $this->request->nested('tr', $lang, $field->name) ?? '';
                    $values['translations'][$lang][$field->name] = $value;
                    $key = "tr.{$lang}.{$field->name}";
                    // Seul le français est obligatoire : l'anglais vide est signalé « incomplet » et remplacé par le français sur le site.
                    if ($field->required && $lang === 'fr' && $value === '') {
                        $errors[$key] = self::message('required');
                    } elseif ($field->max !== null && mb_strlen($value) > $field->max) {
                        $errors[$key] = self::message('max', $field->max);
                    }
                }
                continue;
            }

            if ($field->isFile()) {
                continue;
            }

            [$value, $error] = $this->readField($field);
            $values[$field->name] = $value;
            if ($error !== null) {
                $errors[$field->name] = self::message($error, $field->max);
            }
        }

        return [$values, $errors];
    }

    public static function message(string $key, ?int $n = null): string
    {
        return t('admin.form.errors.' . $key, ['n' => (string) $n]);
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

            return [array_values(array_unique(array_filter($tags, fn ($t) => $t !== '' && mb_strlen($t) <= 40))), null];
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
            'select', 'logo' => [$value, array_key_exists($value, $field->options) ? null : 'select'],
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
     * @param list<Field>          $fields
     * @param array<string, mixed> $values
     * @param array<string, mixed> $previous
     * @return array<string, mixed>
     * @throws \App\Core\UploadException
     */
    public function storeFiles(array $fields, array $values, array $previous): array
    {
        foreach ($fields as $field) {
            if (!$field->isFile()) {
                continue;
            }

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
                $values[$field->name] = $this->uploader->store($upload, $field->type === 'pdf' ? 'pdf' : 'image')->id;
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
     * @param list<Field>          $fields
     * @param array<string, mixed> $previous
     * @param array<string, mixed> $values
     */
    public function deleteReplacedFiles(array $fields, array $previous, array $values): void
    {
        foreach ($fields as $field) {
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

    public function deleteFile(int $fileId): void
    {
        $file = $this->files->find($fileId);
        if ($file !== null) {
            $this->uploader->delete($file);
        }
    }

    /**
     * Langues dont un champ traduit obligatoire est vide (point danger sur l'onglet).
     *
     * @param list<Field>          $fields
     * @param array<string, mixed> $values
     * @return list<string>
     */
    public static function incompleteLangs(array $fields, array $values): array
    {
        $incomplete = [];
        foreach (TranslatableRepository::LANGS as $lang) {
            foreach ($fields as $field) {
                if ($field->translated && $field->required && trim((string) ($values['translations'][$lang][$field->name] ?? '')) === '') {
                    $incomplete[] = $lang;
                    break;
                }
            }
        }

        return $incomplete;
    }
}
