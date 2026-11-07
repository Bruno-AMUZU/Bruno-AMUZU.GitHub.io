<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Limite le nombre de tentatives sur une période (connexion, demandes de lien, formulaire de contact).
 *
 *   if ($throttle->tooMany('login:' . $ip, 20, 900)) { … refuser … }
 *   $throttle->hit('login:' . $ip);
 */
final class Throttle
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function hit(string $key): void
    {
        $this->db->prepare('INSERT INTO throttle_hits (key) VALUES (:key)')->execute(['key' => $key]);

        // Ménage occasionnel des entrées de plus d'un jour.
        if (random_int(1, 50) === 1) {
            $this->db->exec("DELETE FROM throttle_hits WHERE created_at < now() - interval '1 day'");
        }
    }

    public function tooMany(string $key, int $max, int $seconds): bool
    {
        $stmt = $this->db->prepare(
            'SELECT count(*) FROM throttle_hits WHERE key = :key AND created_at > now() - make_interval(secs => :seconds)'
        );
        $stmt->execute(['key' => $key, 'seconds' => $seconds]);

        return (int) $stmt->fetchColumn() >= $max;
    }

    public function clear(string $key): void
    {
        $this->db->prepare('DELETE FROM throttle_hits WHERE key = :key')->execute(['key' => $key]);
    }
}
