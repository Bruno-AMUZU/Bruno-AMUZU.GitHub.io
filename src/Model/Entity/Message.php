<?php

declare(strict_types=1);

namespace App\Model\Entity;

/** Message envoyé par le formulaire de contact. */
final class Message
{
    /** Types de projet du formulaire, dans l'ordre de form.types (Site vitrine, Application, IA, Autre). */
    public const TYPES = ['site', 'app', 'ia', 'other'];

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $type,
        public readonly string $body,
        public readonly string $status,
        public readonly string $createdAt,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['email'],
            (string) $row['type'],
            (string) $row['body'],
            (string) $row['status'],
            (string) $row['created_at'],
        );
    }

    /** Libellé du type de projet (form.types). */
    public function typeLabel(): string
    {
        $index = array_search($this->type, self::TYPES, true);

        return (string) (t_list('form.types')[$index === false ? 3 : $index] ?? $this->type);
    }

    public function excerpt(int $length = 90): string
    {
        $text = (string) preg_replace('/\s+/', ' ', $this->body);

        return mb_strlen($text) > $length ? rtrim(mb_substr($text, 0, $length)) . '…' : $text;
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }
}
