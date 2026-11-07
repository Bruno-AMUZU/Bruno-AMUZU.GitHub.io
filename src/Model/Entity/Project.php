<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Projet du portfolio, avec ses textes dans la langue demandée. */
final class Project
{
    public const TYPES = ['site', 'app', 'ia'];

    /**
     * @param list<string> $technologies
     * @param list<string> $gallery URL des images de la galerie
     */
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly ?int $year,
        public readonly string $type,
        public readonly ?string $imageUrl,
        public readonly ?string $githubUrl,
        public readonly ?string $demoUrl,
        public readonly string $status,
        public readonly string $title,
        public readonly string $summary,
        public readonly string $context,
        public readonly string $solution,
        public readonly string $result,
        public readonly array $technologies = [],
        public readonly array $gallery = [],
    ) {
    }

    /** @param array<string, mixed> $row ligne SQL (textes déjà dans la bonne langue) */
    public static function fromRow(array $row, array $technologies = [], array $gallery = []): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['slug'],
            $row['year'] !== null ? (int) $row['year'] : null,
            (string) $row['type'],
            !empty($row['image_path']) ? '/uploads/' . $row['image_path'] : null,
            $row['github_url'] ?: null,
            $row['demo_url'] ?: null,
            (string) $row['status'],
            (string) $row['title'],
            (string) $row['summary'],
            (string) $row['context'],
            (string) $row['solution'],
            (string) $row['result'],
            $technologies,
            $gallery,
        );
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
