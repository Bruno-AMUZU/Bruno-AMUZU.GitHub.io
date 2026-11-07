<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

/**
 * Applique les migrations SQL de database/migrations/, dans l'ordre de leur nom (001_…, 002_…).
 *
 * Chaque migration appliquée est notée dans la table schema_migrations : relancer ne rejoue rien.
 * Chaque fichier passe dans une transaction : s'il échoue, rien de ce fichier n'est conservé
 * (PostgreSQL annule aussi les CREATE TABLE / ALTER TABLE).
 */
final class Migrator
{
    public function __construct(
        private readonly PDO $db,
        private readonly string $directory,
    ) {
    }

    /**
     * @param (callable(string): void)|null $log reçoit le nom de chaque migration appliquée
     * @return list<string> migrations appliquées pendant cet appel
     */
    public function migrate(?callable $log = null): array
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                name       text PRIMARY KEY,
                applied_at timestamptz NOT NULL DEFAULT now()
            )'
        );

        $done = $this->db->query('SELECT name FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
        $applied = [];

        foreach ($this->pending($done) as $name => $file) {
            $this->db->beginTransaction();
            try {
                $this->db->exec((string) file_get_contents($file));
                $this->db->prepare('INSERT INTO schema_migrations (name) VALUES (:name)')->execute(['name' => $name]);
                $this->db->commit();
            } catch (Throwable $e) {
                $this->db->rollBack();
                throw new \RuntimeException("Échec de la migration {$name} : {$e->getMessage()}", 0, $e);
            }

            $applied[] = $name;
            if ($log !== null) {
                $log($name);
            }
        }

        return $applied;
    }

    /**
     * @param list<string> $done
     * @return array<string, string> nom → chemin, triés par nom
     */
    private function pending(array $done): array
    {
        $files = glob($this->directory . '/*.sql') ?: [];
        sort($files, SORT_STRING);

        $pending = [];
        foreach ($files as $file) {
            $name = basename($file, '.sql');
            if (!in_array($name, $done, true)) {
                $pending[$name] = $file;
            }
        }

        return $pending;
    }
}
