<?php

declare(strict_types=1);

/*
 * Applique les migrations en attente.
 *   docker compose exec app php bin/migrate.php
 */

use App\Core\Migrator;

$container = require __DIR__ . '/bootstrap.php';

$migrator = new Migrator($container->get(PDO::class), ROOT_PATH . '/database/migrations');
$applied = $migrator->migrate(fn (string $name) => print("  ✓ {$name}\n"));

echo $applied === [] ? "Base à jour, aucune migration à appliquer.\n" : count($applied) . " migration(s) appliquée(s).\n";
