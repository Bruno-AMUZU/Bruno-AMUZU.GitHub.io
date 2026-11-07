<?php

declare(strict_types=1);

namespace App\Model\Repository;

use PDO;

/**
 * Liens « mot de passe oublié » : jeton aléatoire envoyé par e-mail, seule son empreinte SHA-256
 * est gardée en base (une fuite de la base ne permet pas d'utiliser les liens). Usage unique, 30 minutes.
 */
final class PasswordResetRepository
{
    public const LIFETIME_MINUTES = 30;

    public function __construct(private readonly PDO $db)
    {
    }

    /** Crée un lien pour cet admin (les liens précédents sont annulés) et renvoie le jeton en clair. */
    public function create(int $adminId): string
    {
        $token = bin2hex(random_bytes(32));
        $this->db->prepare('DELETE FROM password_resets WHERE admin_id = :id')->execute(['id' => $adminId]);
        $this->db->prepare(
            "INSERT INTO password_resets (admin_id, token_hash, expires_at)
             VALUES (:id, :hash, now() + make_interval(mins => :minutes))"
        )->execute(['id' => $adminId, 'hash' => hash('sha256', $token), 'minutes' => self::LIFETIME_MINUTES]);

        return $token;
    }

    /** Identifiant de l'admin si le jeton est valable (non utilisé, non expiré), sinon null. */
    public function adminIdFor(string $token): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT admin_id FROM password_resets
             WHERE token_hash = :hash AND used_at IS NULL AND expires_at > now()'
        );
        $stmt->execute(['hash' => hash('sha256', $token)]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function consume(string $token): void
    {
        $this->db->prepare('UPDATE password_resets SET used_at = now() WHERE token_hash = :hash')
            ->execute(['hash' => hash('sha256', $token)]);
    }
}
