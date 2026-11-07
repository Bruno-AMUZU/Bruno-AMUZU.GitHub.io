<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session PHP sécurisée, démarrée seulement quand on s'en sert (pas de cookie pour un simple visiteur).
 *
 * Messages flash : valeurs gardées pour la requête suivante uniquement (« Message envoyé »,
 * erreurs et anciennes valeurs d'un formulaire après une redirection).
 */
final class Session
{
    private const FLASH = '_flash';
    private const FLASH_NEXT = '_flash_next';

    private bool $started = false;

    public function __construct(private readonly bool $secureCookie = false)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /** Nouvel identifiant de session, à appeler à la connexion (empêche la fixation de session). */
    public function regenerate(): void
    {
        $this->start();
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $this->start();
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/']);
        $this->started = false;
    }

    /** Valeur disponible à la prochaine requête seulement. */
    public function flash(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[self::FLASH_NEXT][$key] = $value;
    }

    /** Valeur flash posée par la requête précédente. */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $this->start();

        return $_SESSION[self::FLASH][$key] ?? $default;
    }

    /**
     * Erreurs de validation et anciennes valeurs, pour réafficher un formulaire après redirection.
     *
     * @param array<string, string> $errors champ → clé du message
     * @param array<string, mixed>  $input
     */
    public function flashForm(array $errors, array $input): void
    {
        $this->flash('errors', $errors);
        $this->flash('old', $input);
    }

    public function started(): bool
    {
        return $this->started;
    }

    private function start(): void
    {
        if ($this->started) {
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('portfolio_session');
            session_start([
                'cookie_httponly' => true,   // inaccessible au JavaScript
                'cookie_secure' => $this->secureCookie, // HTTPS uniquement en production
                'cookie_samesite' => 'Lax',  // non envoyé par les formulaires d'autres sites
                'use_strict_mode' => true,   // refuse les identifiants inventés
                'use_only_cookies' => true,
            ]);
        }
        $this->started = true;

        // Les flashs de la requête précédente deviennent lisibles, puis seront oubliés.
        $_SESSION[self::FLASH] = $_SESSION[self::FLASH_NEXT] ?? [];
        unset($_SESSION[self::FLASH_NEXT]);
    }
}
