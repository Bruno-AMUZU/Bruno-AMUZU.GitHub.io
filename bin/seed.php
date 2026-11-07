<?php

declare(strict_types=1);

/*
 * Remplit la base de développement avec les données de démonstration.
 *   docker compose exec app php bin/seed.php           ajoute les données
 *   docker compose exec app php bin/seed.php --fresh   vide la base, rejoue les migrations, puis ajoute les données
 *
 * Chaque fichier de database/seeds/ (dans l'ordre de leur nom) renvoie une fonction qui reçoit le PDO.
 * Refusé hors développement : ce script peut effacer toute la base.
 */

use App\Core\Env;
use App\Core\Migrator;

$container = require __DIR__ . '/bootstrap.php';

if (Env::get('APP_ENV') !== 'dev') {
    exit("Refusé : bin/seed.php ne s'utilise qu'en développement (APP_ENV=dev).\n");
}

/** @var PDO $db */
$db = $container->get(PDO::class);

if (in_array('--fresh', $argv, true)) {
    $db->exec('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');
    echo "Base vidée.\n";
    (new Migrator($db, ROOT_PATH . '/database/migrations'))->migrate(fn (string $n) => print("  ✓ migration {$n}\n"));
}

require_once ROOT_PATH . '/database/seeds/helpers.php';

$files = glob(ROOT_PATH . '/database/seeds/[0-9][0-9][0-9]_*.php') ?: [];
sort($files, SORT_STRING);

foreach ($files as $file) {
    $seed = require $file;
    $db->beginTransaction();
    try {
        $seed($db);
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e;
    }
    echo '  ✓ seed ' . basename($file, '.php') . "\n";
}

echo count($files) . " seed(s) exécuté(s).\n";
