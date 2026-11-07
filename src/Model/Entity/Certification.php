<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Certification obtenue (page À propos). */
final class Certification
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $obtainedOn,
        public readonly string $organization,
        public readonly ?string $fileUrl,
        public readonly ?string $url,
        public readonly string $title,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            $row['obtained_on'] ?: null,
            (string) $row['organization'],
            !empty($row['file_path']) ? '/uploads/' . $row['file_path'] : null,
            $row['url'] ?: null,
            (string) $row['title'],
        );
    }

    /** Lien « Voir le certificat » : le PDF s'il existe, sinon le lien en ligne. */
    public function link(): ?string
    {
        return $this->fileUrl ?? $this->url;
    }
}
