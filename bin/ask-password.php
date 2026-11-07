<?php

declare(strict_types=1);

/*
 * Demande un nouveau mot de passe dans le terminal (saisie masquée, confirmation, 12 caractères minimum).
 * Utilisé par create-admin.php et reset-password.php ; renvoie le mot de passe.
 * En dehors d'un terminal interactif (tests, scripts), le lit dans la variable ADMIN_PASSWORD.
 */

$read = function (string $prompt): string {
    echo $prompt;
    $interactive = stream_isatty(STDIN);
    if ($interactive) {
        shell_exec('stty -echo');
    }
    $value = rtrim((string) fgets(STDIN), "\r\n");
    if ($interactive) {
        shell_exec('stty echo');
        echo "\n";
    }

    return $value;
};

$password = getenv('ADMIN_PASSWORD') ?: '';
if ($password === '') {
    $password = $read('Mot de passe (12 caractères minimum) : ');
    if ($read('Confirmer le mot de passe : ') !== $password) {
        exit("Les deux mots de passe sont différents.\n");
    }
}

if (mb_strlen($password) < 12) {
    exit("Mot de passe trop court : 12 caractères minimum.\n");
}

return $password;
