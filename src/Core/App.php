<?php

declare(strict_types=1);

namespace App\Core;

use LogicException;

/**
 * Accès au conteneur pour les fonctions des vues (old(), t(), csrf_field()…), qui ne peuvent pas
 * recevoir leurs dépendances par un constructeur. Le reste du code reçoit ses dépendances par injection.
 */
final class App
{
    private static ?Container $container = null;

    public static function boot(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public static function get(string $id): object
    {
        return (self::$container ?? throw new LogicException('Application non démarrée (App::boot).'))->get($id);
    }
}
