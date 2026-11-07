<?php

declare(strict_types=1);

/*
 * Compte admin de développement, défini dans .env (SEED_ADMIN_EMAIL, SEED_ADMIN_PASSWORD).
 * N'existe qu'en développement : bin/seed.php refuse de tourner ailleurs.
 */

use App\Core\Env;

return function (PDO $db): void {
    $email = Env::get('SEED_ADMIN_EMAIL', 'admin@portfolio.test');
    $password = Env::get('SEED_ADMIN_PASSWORD', '');
    if ($password === '' || $password === null) {
        echo "    (pas de compte admin : SEED_ADMIN_PASSWORD est vide dans .env)\n";

        return;
    }

    $db->prepare(
        'INSERT INTO admins (email, name, password_hash) VALUES (:email, :name, :hash)
         ON CONFLICT (lower(email)) DO NOTHING'
    )->execute(['email' => $email, 'name' => 'Admin (dev)', 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
};
