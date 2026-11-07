<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Middleware des routes /admin : sans connexion, redirection vers la page de connexion.
 * Les pages du back-office ne sont jamais mises en cache ni indexées.
 */
final class RequireAdmin
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function handle(Request $request): ?Response
    {
        if ($this->auth->check()) {
            return null;
        }

        return Response::redirect('/admin/connexion', 303);
    }
}
