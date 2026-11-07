<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Admin;
use PDO;

final class AdminRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function find(int $id): ?Admin
    {
        $stmt = $this->db->prepare('SELECT * FROM admins WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Admin::fromRow($row) : null;
    }

    public function findByEmail(string $email): ?Admin
    {
        $stmt = $this->db->prepare('SELECT * FROM admins WHERE lower(email) = lower(:email)');
        $stmt->execute(['email' => trim($email)]);
        $row = $stmt->fetch();

        return $row ? Admin::fromRow($row) : null;
    }

    public function create(string $email, string $name, string $password): Admin
    {
        $stmt = $this->db->prepare(
            'INSERT INTO admins (email, name, password_hash) VALUES (:email, :name, :hash) RETURNING *'
        );
        $stmt->execute(['email' => trim($email), 'name' => trim($name), 'hash' => self::hash($password)]);

        return Admin::fromRow($stmt->fetch());
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->db->prepare('UPDATE admins SET password_hash = :hash WHERE id = :id')
            ->execute(['hash' => self::hash($password), 'id' => $id]);
    }

    /** Ré-enregistre l'empreinte si l'algorithme par défaut de PHP a changé (password_needs_rehash). */
    public function rehashIfNeeded(Admin $admin, string $password): void
    {
        if (password_needs_rehash($admin->passwordHash, PASSWORD_DEFAULT)) {
            $this->updatePassword($admin->id, $password);
        }
    }

    public function touchLogin(int $id): void
    {
        $this->db->prepare('UPDATE admins SET last_login_at = now() WHERE id = :id')->execute(['id' => $id]);
    }

    private static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
