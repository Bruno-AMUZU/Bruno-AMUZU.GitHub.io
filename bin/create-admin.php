<?php

declare(strict_types=1);

/*
 * Crée un compte du back-office.
 *   docker compose exec app php bin/create-admin.php email@exemple.fr "Bruno Amuzu"
 *
 * Le mot de passe est demandé sans être affiché, deux fois. 12 caractères minimum.
 */

use App\Model\Repository\AdminRepository;

$container = require __DIR__ . '/bootstrap.php';

$email = $argv[1] ?? '';
$name = $argv[2] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Usage : php bin/create-admin.php email@exemple.fr \"Nom\"\n");
}

/** @var AdminRepository $admins */
$admins = $container->get(AdminRepository::class);
if ($admins->findByEmail($email) !== null) {
    exit("Un compte existe déjà pour {$email}. Pour changer son mot de passe : php bin/reset-password.php {$email}\n");
}

$password = require __DIR__ . '/ask-password.php';
$admins->create($email, $name, $password);

echo "Compte créé pour {$email}. Connexion : /admin/connexion\n";
