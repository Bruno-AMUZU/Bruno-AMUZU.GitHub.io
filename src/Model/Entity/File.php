<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Fichier envoyé depuis le back-office (image ou PDF), stocké dans storage/uploads/. */
final class File
{
    public function __construct(
        public readonly int $id,
        public readonly string $path,
        public readonly string $originalName,
        public readonly string $mime,
        public readonly int $size,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['path'],
            (string) $row['original_name'],
            (string) $row['mime'],
            (int) $row['size'],
            isset($row['width']) ? (int) $row['width'] : null,
            isset($row['height']) ? (int) $row['height'] : null,
        );
    }

    /** Adresse publique du fichier. */
    public function url(): string
    {
        return '/uploads/' . $this->path;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }
}
