<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Compétence / outil : une ligne (TechChip) d'une carte de la Stack. */
final class Skill
{
    /** Catégories, dans l'ordre des cartes de la Stack ; « ai » est la carte inversée. */
    public const CATEGORIES = ['front', 'back', 'tools', 'ai'];

    public function __construct(
        public readonly int $id,
        public readonly string $category,
        public readonly ?string $tech,
        public readonly ?string $mono,
        public readonly string $name,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self((int) $row['id'], (string) $row['category'], $row['tech'] ?: null, $row['mono'] ?: null, (string) $row['name']);
    }

    /** Propriétés du composant TechChip. @return array{name: string, tech: ?string, mono: ?string} */
    public function chip(): array
    {
        return ['name' => $this->name, 'tech' => $this->tech, 'mono' => $this->mono];
    }
}
