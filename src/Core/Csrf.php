<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Protection CSRF : un autre site ne peut pas faire envoyer un de nos formulaires par le navigateur
 * du visiteur, car il ne connaît pas le jeton secret stocké dans sa session.
 *
 * Chaque formulaire POST contient <?= csrf_field() ?> ; toute requête POST est vérifiée par check().
 */
final class Csrf
{
    public const FIELD = '_csrf';
    private const KEY = '_csrf_token';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::KEY);
        if (!is_string($token)) {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::KEY, $token);
        }

        return $token;
    }

    /** @throws HttpException 403 si le jeton est absent ou faux */
    public function check(Request $request): void
    {
        if (!$request->isPost()) {
            return;
        }

        $sent = $request->input(self::FIELD) ?? $request->header('X-CSRF-Token') ?? '';
        $expected = $this->session->get(self::KEY);

        // hash_equals compare en temps constant : la durée ne révèle rien du jeton.
        if (!is_string($expected) || !hash_equals($expected, $sent)) {
            throw new HttpException(403, message: 'Jeton CSRF invalide');
        }
    }
}
