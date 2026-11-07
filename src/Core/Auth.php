<?php

declare(strict_types=1);

namespace App\Core;

use App\Model\Entity\Admin;
use App\Model\Repository\AdminRepository;

/**
 * Connexion au back-office.
 *
 * - Mot de passe vérifié par password_verify ; si l'adresse n'existe pas, on vérifie quand même
 *   une empreinte factice pour que la durée de réponse ne révèle pas quelles adresses existent.
 * - À la connexion, nouvel identifiant de session (contre la fixation de session).
 * - Déconnexion automatique après 2 heures d'inactivité.
 */
final class Auth
{
    private const KEY = 'admin_id';
    private const LAST_SEEN = 'admin_last_seen';
    public const IDLE_TIMEOUT = 7200;

    private ?Admin $admin = null;

    public function __construct(
        private readonly Session $session,
        private readonly AdminRepository $admins,
    ) {
    }

    public function attempt(string $email, string $password): ?Admin
    {
        $admin = $this->admins->findByEmail($email);
        // Empreinte bcrypt valide d'un mot de passe aléatoire inconnu : même coût de calcul qu'un vrai compte.
        $hash = $admin?->passwordHash ?? '$2y$12$XpFyTYuOfs3oJCrjbVaPqOfMItCjx7RQsx2.uHU08U9/.M1C2AAti';

        if (!password_verify($password, $hash) || $admin === null) {
            return null;
        }

        $this->admins->rehashIfNeeded($admin, $password);

        return $admin;
    }

    public function login(Admin $admin): void
    {
        $this->session->regenerate();
        $this->session->set(self::KEY, $admin->id);
        $this->session->set(self::LAST_SEEN, time());
        $this->admins->touchLogin($admin->id);
        $this->admin = $admin;
    }

    public function logout(): void
    {
        $this->admin = null;
        $this->session->destroy();
    }

    public function admin(): ?Admin
    {
        if ($this->admin !== null) {
            return $this->admin;
        }

        $id = $this->session->get(self::KEY);
        if (!is_int($id)) {
            return null;
        }

        $lastSeen = (int) $this->session->get(self::LAST_SEEN, 0);
        if (time() - $lastSeen > self::IDLE_TIMEOUT) {
            $this->logout();

            return null;
        }
        $this->session->set(self::LAST_SEEN, time());

        return $this->admin = $this->admins->find($id);
    }

    public function check(): bool
    {
        return $this->admin() !== null;
    }
}
