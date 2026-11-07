<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\File;
use PDO;

final class FileRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function find(int $id): ?File
    {
        $stmt = $this->db->prepare('SELECT * FROM files WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? File::fromRow($row) : null;
    }

    public function findByPath(string $path): ?File
    {
        $stmt = $this->db->prepare('SELECT * FROM files WHERE path = :path');
        $stmt->execute(['path' => $path]);
        $row = $stmt->fetch();

        return $row ? File::fromRow($row) : null;
    }

    public function create(string $path, string $originalName, string $mime, int $size, ?int $width, ?int $height): File
    {
        $stmt = $this->db->prepare(
            'INSERT INTO files (path, original_name, mime, size, width, height)
             VALUES (:path, :original_name, :mime, :size, :width, :height)
             RETURNING *'
        );
        $stmt->execute([
            'path' => $path,
            'original_name' => $originalName,
            'mime' => $mime,
            'size' => $size,
            'width' => $width,
            'height' => $height,
        ]);

        return File::fromRow($stmt->fetch());
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM files WHERE id = :id')->execute(['id' => $id]);
    }
}
