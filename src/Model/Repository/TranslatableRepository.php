<?php

declare(strict_types=1);

namespace App\Model\Repository;

use PDO;
use Throwable;

/**
 * Base des types de contenu bilingues : une table principale (projects) + une table de traductions
 * (project_translations, une ligne par langue).
 *
 * La table principale a toujours : id, position, created_at, updated_at (et status si hasStatus()).
 * Chaque type déclare ses colonnes ; le SQL commun (liste, lecture, enregistrement, suppression,
 * ordre) est écrit une seule fois ici. Les noms de tables et de colonnes viennent du code, jamais
 * de l'utilisateur ; les valeurs passent toujours par des requêtes préparées.
 */
abstract class TranslatableRepository implements ResourceRepository
{
    public const LANGS = ['fr', 'en'];

    public function __construct(protected readonly PDO $db)
    {
    }

    /** Table principale, ex. 'projects'. */
    abstract protected function table(): string;

    /** Table des traductions, ex. 'project_translations'. */
    abstract protected function translationTable(): string;

    /** Colonne de la table des traductions qui pointe vers la table principale, ex. 'project_id'. */
    abstract protected function foreignKey(): string;

    /** @return list<string> colonnes communes modifiables depuis le back-office */
    abstract protected function columns(): array;

    /** @return list<string> colonnes traduites */
    abstract protected function translatedColumns(): array;

    /** Colonne traduite qui sert de titre dans la liste du back-office. */
    protected function titleColumn(): string
    {
        return 'title';
    }

    protected function hasStatus(): bool
    {
        return true;
    }

    /** Colonne (id de fichier) affichée en miniature dans la liste, ou null. */
    protected function thumbColumn(): ?string
    {
        return null;
    }

    /** @return list<string> colonnes contenant un id de fichier (supprimés avec le contenu) */
    protected function fileColumns(): array
    {
        return [];
    }

    /** @param array<string, mixed> $values */
    protected function loadRelations(int $id, array &$values): void
    {
    }

    /** @param array<string, mixed> $values */
    protected function saveRelations(int $id, array $values): void
    {
    }

    /** @return list<int> fichiers référencés par les tables liées (galerie…) */
    protected function relationFileIds(int $id): array
    {
        return [];
    }

    public function adminList(?string $search, ?string $status): array
    {
        $t = $this->table();
        $tr = $this->translationTable();
        $fk = $this->foreignKey();
        $title = $this->titleColumn();
        $thumb = $this->thumbColumn();

        $sql = "SELECT m.id, m.updated_at, " . ($this->hasStatus() ? 'm.status' : 'NULL AS status') . ",
                       fr.{$title} AS title_fr, en.{$title} AS title_en,
                       " . ($thumb ? 'f.path' : 'NULL') . " AS thumb
                FROM {$t} m
                LEFT JOIN {$tr} fr ON fr.{$fk} = m.id AND fr.lang = 'fr'
                LEFT JOIN {$tr} en ON en.{$fk} = m.id AND en.lang = 'en'
                " . ($thumb ? "LEFT JOIN files f ON f.id = m.{$thumb}" : '') . "
                WHERE 1 = 1";
        $params = [];
        if ($search !== null && $search !== '') {
            // Un même paramètre nommé ne peut pas servir deux fois avec de vraies requêtes préparées.
            $sql .= " AND (fr.{$title} ILIKE :q_fr OR en.{$title} ILIKE :q_en)";
            $params['q_fr'] = $params['q_en'] = '%' . addcslashes($search, '%_\\') . '%';
        }
        if ($status !== null && $this->hasStatus()) {
            $sql .= ' AND m.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY m.position, m.id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(fn (array $row): array => [
            'id' => (int) $row['id'],
            'title' => (string) ($row['title_fr'] ?: $row['title_en'] ?: '—'),
            'thumb' => $row['thumb'] ? '/uploads/' . $row['thumb'] : null,
            'langs' => array_keys(array_filter(['fr' => $row['title_fr'], 'en' => $row['title_en']], fn ($v) => (string) $v !== '')),
            'status' => $row['status'],
            'updated_at' => $row['updated_at'],
        ], $stmt->fetchAll());
    }

    public function formValues(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table()} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $values = $row;
        $values['translations'] = array_fill_keys(self::LANGS, []);
        $stmt = $this->db->prepare("SELECT * FROM {$this->translationTable()} WHERE {$this->foreignKey()} = :id");
        $stmt->execute(['id' => $id]);
        foreach ($stmt->fetchAll() as $translation) {
            $values['translations'][$translation['lang']] = $translation;
        }
        $this->loadRelations($id, $values);

        return $values;
    }

    public function save(?int $id, array $values): int
    {
        $columns = $this->columns();
        if ($this->hasStatus()) {
            $columns[] = 'status';
        }
        $data = [];
        foreach ($columns as $column) {
            $data[$column] = $values[$column] ?? null;
        }

        $this->db->beginTransaction();
        try {
            if ($id === null) {
                $table = $this->table();
                $names = implode(', ', array_keys($data));
                $placeholders = implode(', ', array_map(fn ($c) => ':' . $c, array_keys($data)));
                // Nouveau contenu placé en dernier dans l'ordre d'affichage.
                $sql = "INSERT INTO {$table} ({$names}, position)
                        VALUES ({$placeholders}, (SELECT COALESCE(MAX(position), 0) + 1 FROM {$table}))
                        RETURNING id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($data);
                $id = (int) $stmt->fetchColumn();
            } else {
                $sql = sprintf(
                    'UPDATE %s SET %s, updated_at = now() WHERE id = :id',
                    $this->table(),
                    implode(', ', array_map(fn ($c) => "{$c} = :{$c}", array_keys($data))),
                );
                $this->db->prepare($sql)->execute([...$data, 'id' => $id]);
            }

            $this->saveTranslations($id, $values['translations'] ?? []);
            $this->saveRelations($id, $values);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $id;
    }

    public function delete(int $id): array
    {
        $values = $this->formValues($id);
        if ($values === null) {
            return [];
        }
        $files = $this->relationFileIds($id);
        foreach ($this->fileColumns() as $column) {
            if (!empty($values[$column])) {
                $files[] = (int) $values[$column];
            }
        }

        $this->db->prepare("DELETE FROM {$this->table()} WHERE id = :id")->execute(['id' => $id]);

        return array_values(array_unique($files));
    }

    public function reorder(array $ids): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table()} SET position = :position WHERE id = :id");
        $this->db->beginTransaction();
        foreach (array_values($ids) as $position => $id) {
            $stmt->execute(['position' => $position + 1, 'id' => (int) $id]);
        }
        $this->db->commit();
    }

    public function slugTaken(string $slug, ?int $exceptId): bool
    {
        if (!in_array('slug', $this->columns(), true)) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT 1 FROM {$this->table()} WHERE slug = :slug AND id <> :id");
        $stmt->execute(['slug' => $slug, 'id' => $exceptId ?? 0]);

        return (bool) $stmt->fetchColumn();
    }

    /** @param array<string, array<string, mixed>> $translations */
    private function saveTranslations(int $id, array $translations): void
    {
        $columns = $this->translatedColumns();
        $fk = $this->foreignKey();
        $table = $this->translationTable();
        $names = implode(', ', $columns);
        $placeholders = implode(', ', array_map(fn ($c) => ':' . $c, $columns));
        $updates = implode(', ', array_map(fn ($c) => "{$c} = EXCLUDED.{$c}", $columns));
        $sql = "INSERT INTO {$table} ({$fk}, lang, {$names}) VALUES (:fk, :lang, {$placeholders})
                ON CONFLICT ({$fk}, lang) DO UPDATE SET {$updates}";
        $stmt = $this->db->prepare($sql);

        foreach (self::LANGS as $lang) {
            $row = ['fk' => $id, 'lang' => $lang];
            foreach ($columns as $column) {
                $row[$column] = (string) ($translations[$lang][$column] ?? '');
            }
            $stmt->execute($row);
        }
    }

    /**
     * Texte traduit avec repli sur le français : une traduction anglaise vide affiche le français.
     * À utiliser dans les requêtes du site : COALESCE(NULLIF(cur.title, ''), fr.title).
     */
    protected function localized(string $column, string $current = 'cur', string $fallback = 'fr'): string
    {
        return "COALESCE(NULLIF({$current}.{$column}, ''), {$fallback}.{$column}) AS {$column}";
    }
}
