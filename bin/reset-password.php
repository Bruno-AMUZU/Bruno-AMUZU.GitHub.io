<?php

declare(strict_types=1);

/*
 * Secours si l'e-mail de réinitialisation ne fonctionne pas : change le mot de passe d'un compte
 * directement sur le serveur.
 *   docker compose exec app php bin/reset-password.php email@exemple.fr
 */

use App\Model\Repository\AdminRepository;

$container = require __DIR__ . '/bootstrap.php';

$email = $argv[1] ?? '';
/** @var AdminRepository $admins */
$admins = $container->get(AdminRepository::class);
$admin = $admins->findByEmail($email);
if ($admin === null) {
    exit("Aucun compte pour « {$email} ». Usage : php bin/reset-password.php email@exemple.fr\n");
}

$password = require __DIR__ . '/ask-password.php';
$admins->updatePassword($admin->id, $password);

echo "Mot de passe changé pour {$admin->email}.\n";
