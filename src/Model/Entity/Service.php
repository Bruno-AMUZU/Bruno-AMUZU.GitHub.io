<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Service présenté sur l'accueil (ServiceCard). */
final class Service
{
    public const ILLUSTRATIONS = ['web', 'apps', 'ia'];

    public function __construct(
        public readonly int $id,
        public readonly string $illustration,
        public readonly string $title,
        public readonly string $description,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self((int) $row['id'], (string) $row['illustration'], (string) $row['title'], (string) $row['description']);
    }

    /** La carte IA est la carte inversée (components.md → ServiceCard). */
    public function isInverted(): bool
    {
        return $this->illustration === 'ia';
    }
}
