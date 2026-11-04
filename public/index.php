<?php

declare(strict_types=1);

/*
 * Point d'entrée unique : toutes les requêtes arrivent ici (voir public/.htaccess).
 */

use App\Core\Router;

// Racine du projet, pour construire les chemins (views/, lang/, storage/…) sans dépendre de l'emplacement du fichier.
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

$router = new Router();

// La racine redirige vers la version française.
$router->get('/', function (): void {
    header('Location: /fr', true, 302);
});

// Page temporaire pour vérifier que le routage fonctionne ; à remplacer par le contrôleur de l'accueil.
$router->get('/{lang:fr|en}', function (string $lang): void {
    echo $lang === 'fr' ? 'Bonjour 👋' : 'Hello 👋';
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
