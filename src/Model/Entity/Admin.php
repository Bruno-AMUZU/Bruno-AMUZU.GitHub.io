<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Compte du back-office. */
final class Admin
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly string $passwordHash,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self((int) $row['id'], (string) $row['email'], (string) $row['name'], (string) $row['password_hash']);
    }
}
