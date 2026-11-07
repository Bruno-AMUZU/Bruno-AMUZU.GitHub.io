<?php

declare(strict_types=1);

/*
 * Supprime les messages de contact de plus de 12 mois (durée annoncée par la mention RGPD).
 * Le site le fait déjà à chaque message reçu et à l'ouverture des Messages du back-office ;
 * ce script le garantit même sans activité, à planifier chaque nuit (voir docs/DEPLOIEMENT.md).
 *   php bin/purge-messages.php
 */

use App\Model\Repository\MessageRepository;

$container = require __DIR__ . '/bootstrap.php';

$deleted = $container->get(MessageRepository::class)->purgeOlderThan();

echo $deleted . " message(s) de plus de " . MessageRepository::RETENTION_MONTHS . " mois supprimé(s).\n";
