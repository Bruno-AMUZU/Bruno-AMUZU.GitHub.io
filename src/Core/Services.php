<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Déclare les services de l'application, au même endroit pour le site, les scripts et les tests.
 */
final class Services
{
    public static function container(?Request $request = null): Container
    {
        $container = new Container();

        // Une seule connexion par requête, ouverte seulement si un repository en a besoin.
        $container->set(PDO::class, fn () => Database::connect());

        $request ??= new Request('GET', '/');
        $container->set(Request::class, fn () => $request);
        $container->set(Session::class, fn () => new Session(secureCookie: $request->isSecure()));
        $container->set(SecurityHeaders::class, fn () => new SecurityHeaders(production: Env::get('APP_ENV') === 'prod'));

        return $container;
    }
}
