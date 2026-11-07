<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Certification;

final class CertificationRepository extends TranslatableRepository
{
    protected function table(): string { return 'certifications'; }
    protected function translationTable(): string { return 'certification_translations'; }
    protected function foreignKey(): string { return 'certification_id'; }
    protected function columns(): array { return ['obtained_on', 'organization', 'file_id', 'url']; }
    protected function translatedColumns(): array { return ['title']; }
    protected function hasStatus(): bool { return false; }
    protected function fileColumns(): array { return ['file_id']; }

    /** @return list<Certification> toutes, dans l'ordre choisi */
    public function all(string $lang): array
    {
        $rows = $this->localizedRows($lang);
        $paths = $this->filePaths(array_filter(array_column($rows, 'file_id')));

        return array_map(
            fn (array $row) => Certification::fromRow([...$row, 'file_path' => $paths[(int) $row['file_id']] ?? null]),
            $rows,
        );
    }

    /**
     * @param array<int|string> $ids
     * @return array<int, string> id → chemin
     */
    private function filePaths(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        $stmt = $this->db->prepare('SELECT id, path FROM files WHERE id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ')');
        $stmt->execute(array_map('intval', array_values($ids)));

        return array_column($stmt->fetchAll(), 'path', 'id');
    }
}
