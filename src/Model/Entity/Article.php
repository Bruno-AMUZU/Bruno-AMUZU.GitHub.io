<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Article du Lab, avec ses textes dans la langue demandée. */
final class Article
{
    /** Catégories, dans l'ordre des filtres du Lab (Back-end, Front-end, IA, Outils). */
    public const CATEGORIES = ['back', 'front', 'ia', 'tools'];

    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $category,
        public readonly ?string $publishedOn,
        public readonly ?int $readingTime,
        public readonly ?string $videoUrl,
        public readonly string $status,
        public readonly string $title,
        public readonly string $excerpt,
        public readonly string $content,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['slug'],
            (string) $row['category'],
            $row['published_on'] ?: null,
            $row['reading_time'] !== null ? (int) $row['reading_time'] : null,
            $row['video_url'] ?: null,
            (string) $row['status'],
            (string) $row['title'],
            (string) $row['excerpt'],
            (string) $row['content'],
        );
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /** Identifiant YouTube de la vidéo (11 caractères), ou null. */
    public function youtubeId(): ?string
    {
        return $this->videoUrl !== null ? self::parseYoutubeId($this->videoUrl) : null;
    }

    /** youtube.com/watch?v=…, youtu.be/…, youtube.com/embed/…, youtube.com/shorts/… → identifiant. */
    public static function parseYoutubeId(string $url): ?string
    {
        $pattern = '#^https?://(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})(?:[?&\#].*)?$#';

        return preg_match($pattern, $url, $m) ? $m[1] : null;
    }
}
