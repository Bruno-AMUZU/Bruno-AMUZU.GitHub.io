<?php

declare(strict_types=1);

namespace App\Model\Repository;

use PDO;

/**
 * Réglages du site (table clé → valeur), lus une seule fois par requête.
 */
final class SettingRepository
{
    /** @var array<string, string>|null */
    private ?array $cache = null;

    public function __construct(private readonly PDO $db)
    {
    }

    public function get(string $key): ?string
    {
        $this->cache ??= $this->all();

        return $this->cache[$key] ?? null;
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return array_column($this->db->query('SELECT key, value FROM settings')->fetchAll(), 'value', 'key');
    }

    /** @param array<string, string|int|bool|null> $values */
    public function save(array $values): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
             ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value, updated_at = now()'
        );
        $this->db->beginTransaction();
        foreach ($values as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? '')]);
        }
        $this->db->commit();
        $this->cache = null;
    }

    /** Valeur d'un réglage contenant un id de fichier : adresse publique du fichier, ou null. */
    public function fileUrl(string $key): ?string
    {
        $id = (int) $this->get($key);
        if ($id === 0) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT path FROM files WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $path = $stmt->fetchColumn();

        return $path ? '/uploads/' . $path : null;
    }
}
