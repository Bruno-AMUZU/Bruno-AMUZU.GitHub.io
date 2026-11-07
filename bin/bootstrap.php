<?php

declare(strict_types=1);

/*
 * Démarrage commun des scripts en ligne de commande (bin/*.php).
 * Équivalent de public/index.php pour le terminal : ROOT_PATH, autoload, .env, conteneur.
 */

use App\Core\Env;
use App\Core\Services;

if (PHP_SAPI !== 'cli') {
    exit("Script réservé à la ligne de commande.\n");
}

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

Env::load(ROOT_PATH . '/.env');

return Services::container();
