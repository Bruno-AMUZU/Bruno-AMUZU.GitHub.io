<?php

declare(strict_types=1);

/*
 * Point d'entrée unique : toutes les requêtes arrivent ici (voir public/.htaccess).
 */

use App\Controller\Site\HomeController;
use App\Core\Env;
use App\Core\Router;
use App\Core\Services;

// Racine du projet, pour construire les chemins (views/, lang/, storage/…) sans dépendre de l'emplacement du fichier.
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

Env::load(ROOT_PATH . '/.env');

$router = new Router(Services::container());

// La racine redirige vers la version française.
$router->get('/', function (): void {
    header('Location: /fr', true, 302);
});

$router->get('/{lang:fr|en}', [HomeController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
