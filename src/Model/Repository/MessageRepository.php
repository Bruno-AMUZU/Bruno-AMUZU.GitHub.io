<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Message;
use PDO;

final class MessageRepository
{
    /** Durée de conservation des messages, annoncée dans la mention RGPD et les mentions légales. */
    public const RETENTION_MONTHS = 12;

    public function __construct(private readonly PDO $db)
    {
    }

    public function create(string $name, string $email, string $type, string $body, string $lang): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (name, email, type, body, lang) VALUES (:name, :email, :type, :body, :lang) RETURNING id'
        );
        $stmt->execute(['name' => $name, 'email' => $email, 'type' => $type, 'body' => $body, 'lang' => $lang]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param 'unread'|'all'|'archived' $filter « Tous » = non archivés
     * @return list<Message> du plus récent au plus ancien
     */
    public function list(string $filter, ?int $limit = null): array
    {
        $where = match ($filter) {
            'unread' => "status = 'unread'",
            'archived' => "status = 'archived'",
            default => "status <> 'archived'",
        };
        $sql = "SELECT * FROM messages WHERE {$where} ORDER BY created_at DESC, id DESC" . ($limit ? ' LIMIT ' . (int) $limit : '');

        return array_map(fn (array $row) => Message::fromRow($row), $this->db->query($sql)->fetchAll());
    }

    public function find(int $id): ?Message
    {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Message::fromRow($row) : null;
    }

    /** @param 'unread'|'read'|'archived' $status */
    public function setStatus(int $id, string $status): void
    {
        $this->db->prepare('UPDATE messages SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM messages WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Supprime les messages reçus il y a plus de $months mois (durée annoncée par la mention RGPD).
     *
     * @return int nombre de messages supprimés
     */
    public function purgeOlderThan(int $months = self::RETENTION_MONTHS): int
    {
        $stmt = $this->db->prepare('DELETE FROM messages WHERE created_at < now() - make_interval(months => :months)');
        $stmt->execute(['months' => $months]);

        return $stmt->rowCount();
    }

    public function countUnread(): int
    {
        return (int) $this->db->query("SELECT count(*) FROM messages WHERE status = 'unread'")->fetchColumn();
    }
}
