<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Project;

/**
 * Projets : back-office (via TranslatableRepository) et pages du site.
 * Sur le site, un texte anglais vide est remplacé par le français.
 */
final class ProjectRepository extends TranslatableRepository
{
    protected function table(): string { return 'projects'; }
    protected function translationTable(): string { return 'project_translations'; }
    protected function foreignKey(): string { return 'project_id'; }
    protected function columns(): array { return ['slug', 'year', 'type', 'image_id', 'github_url', 'demo_url']; }
    protected function translatedColumns(): array { return ['title', 'summary', 'context', 'solution', 'result']; }
    protected function thumbColumn(): ?string { return 'image_id'; }
    protected function fileColumns(): array { return ['image_id']; }

    // ── Site ────────────────────────────────────────────────

    /** @return list<Project> projets publiés, dans l'ordre choisi, éventuellement d'un seul type */
    public function published(string $lang, ?string $type = null, ?int $limit = null): array
    {
        $sql = $this->selectSql() . " WHERE p.status = 'published'" . ($type !== null ? ' AND p.type = :type' : '')
            . ' ORDER BY p.position, p.id' . ($limit !== null ? ' LIMIT ' . (int) $limit : '');
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['lang' => $lang, ...($type !== null ? ['type' => $type] : [])]);
        $rows = $stmt->fetchAll();
        $technologies = $this->technologiesFor(array_column($rows, 'id'));

        return array_map(fn (array $row) => Project::fromRow($row, $technologies[(int) $row['id']] ?? []), $rows);
    }

    /** Projet par son slug ; un brouillon n'est renvoyé que si $withDrafts (aperçu du back-office). */
    public function findBySlug(string $slug, string $lang, bool $withDrafts = false): ?Project
    {
        $sql = $this->selectSql() . ' WHERE p.slug = :slug' . ($withDrafts ? '' : " AND p.status = 'published'");
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['lang' => $lang, 'slug' => $slug]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $id = (int) $row['id'];
        $gallery = $this->db->prepare(
            'SELECT f.path FROM project_images pi JOIN files f ON f.id = pi.file_id WHERE pi.project_id = :id ORDER BY pi.position'
        );
        $gallery->execute(['id' => $id]);

        return Project::fromRow(
            $row,
            $this->technologiesFor([$id])[$id] ?? [],
            array_map(fn ($path) => '/uploads/' . $path, $gallery->fetchAll(\PDO::FETCH_COLUMN)),
        );
    }

    /** Projet publié suivant dans l'ordre (le premier après le dernier). */
    public function next(Project $project, string $lang): ?Project
    {
        $all = $this->published($lang);
        if (count($all) < 2) {
            return null;
        }
        foreach ($all as $i => $candidate) {
            if ($candidate->id === $project->id) {
                return $all[($i + 1) % count($all)];
            }
        }

        return $all[0];
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare('SELECT count(*) FROM projects WHERE status = :status');
        $stmt->execute(['status' => $status]);

        return (int) $stmt->fetchColumn();
    }

    private function selectSql(): string
    {
        return 'SELECT p.*, f.path AS image_path, '
            . implode(', ', array_map(fn ($c) => $this->localized($c), $this->translatedColumns()))
            . ' FROM projects p
                JOIN project_translations fr ON fr.project_id = p.id AND fr.lang = \'fr\'
                LEFT JOIN project_translations cur ON cur.project_id = p.id AND cur.lang = :lang
                LEFT JOIN files f ON f.id = p.image_id';
    }

    /**
     * @param list<int|string> $ids
     * @return array<int, list<string>>
     */
    private function technologiesFor(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT project_id, name FROM project_technologies WHERE project_id IN ({$placeholders}) ORDER BY position");
        $stmt->execute(array_map('intval', $ids));
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int) $row['project_id']][] = (string) $row['name'];
        }

        return $result;
    }

    // ── Back-office : technos et galerie ────────────────────

    protected function loadRelations(int $id, array &$values): void
    {
        $values['technologies'] = $this->technologiesFor([$id])[$id] ?? [];
        $stmt = $this->db->prepare('SELECT file_id FROM project_images WHERE project_id = :id ORDER BY position');
        $stmt->execute(['id' => $id]);
        $values['gallery'] = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    protected function saveRelations(int $id, array $values): void
    {
        $this->db->prepare('DELETE FROM project_technologies WHERE project_id = :id')->execute(['id' => $id]);
        $insert = $this->db->prepare('INSERT INTO project_technologies (project_id, name, position) VALUES (:id, :name, :position)');
        foreach (array_values((array) ($values['technologies'] ?? [])) as $position => $name) {
            $insert->execute(['id' => $id, 'name' => $name, 'position' => $position]);
        }

        $this->db->prepare('DELETE FROM project_images WHERE project_id = :id')->execute(['id' => $id]);
        $insert = $this->db->prepare('INSERT INTO project_images (project_id, file_id, position) VALUES (:id, :file, :position)');
        foreach (array_values(array_unique((array) ($values['gallery'] ?? []))) as $position => $fileId) {
            $insert->execute(['id' => $id, 'file' => (int) $fileId, 'position' => $position]);
        }
    }

    protected function relationFileIds(int $id): array
    {
        $stmt = $this->db->prepare('SELECT file_id FROM project_images WHERE project_id = :id');
        $stmt->execute(['id' => $id]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }
}
