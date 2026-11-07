<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Déclare les services de l'application, au même endroit pour le site, les scripts et les tests.
 */
final class Services
{
    public static function container(): Container
    {
        $container = new Container();

        // Une seule connexion par requête, ouverte seulement si un repository en a besoin.
        $container->set(PDO::class, fn () => Database::connect());

        return $container;
    }
}
