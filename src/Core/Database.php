<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Crée la connexion PDO à PostgreSQL à partir de la configuration (.env).
 *
 * La connexion n'est pas une variable globale : elle est créée une fois par le conteneur
 * (voir Services) puis passée aux repositories qui en ont besoin (injection de dépendances).
 */
final class Database
{
    public static function connect(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            Env::require('DB_HOST'),
            Env::get('DB_PORT', '5432'),
            Env::require('DB_NAME'),
        );

        return new PDO($dsn, Env::require('DB_USER'), Env::require('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // erreur SQL = exception, jamais d'échec silencieux
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // lignes en tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES => false,               // vraies requêtes préparées côté PostgreSQL
        ]);
    }
}
