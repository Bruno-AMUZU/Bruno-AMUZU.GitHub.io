<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Lit la configuration dans les variables d'environnement.
 *
 * En développement, Docker les fournit déjà (env_file: .env). Ailleurs (hébergement sans Docker,
 * scripts lancés à la main), Env::load() lit le fichier .env. Une variable déjà définie par le
 * système n'est jamais écrasée par le fichier.
 */
final class Env
{
    public static function load(string $file): void
    {
        if (!is_file($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));
            $value = preg_replace('/^(["\'])(.*)\1$/', '$2', $value) ?? $value; // retire les guillemets

            if (getenv($name) === false) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
            }
        }
    }

    /** Valeur de la variable, ou $default si elle n'existe pas. */
    public static function get(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);

        return $value === false ? $default : $value;
    }

    /** Valeur obligatoire : erreur explicite si elle manque. */
    public static function require(string $name): string
    {
        return self::get($name) ?? throw new RuntimeException("Variable d'environnement manquante : {$name}");
    }
}
