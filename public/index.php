<?php

declare(strict_types=1);

/*
 * Point d'entrée unique : toutes les requêtes arrivent ici (voir public/.htaccess).
 */

use App\Controller\Site\HomeController;
use App\Core\App;
use App\Core\Env;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Services;

// Racine du projet, pour construire les chemins (views/, lang/, storage/…) sans dépendre de l'emplacement du fichier.
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

Env::load(ROOT_PATH . '/.env');

$request = Request::fromGlobals();
$container = Services::container($request);
App::boot($container);

$router = new Router($container);

// La racine redirige vers la version française.
$router->get('/', fn () => Response::redirect('/fr', 302));

$router->get('/{lang:fr|en}', [HomeController::class, 'index']);

try {
    $response = $router->dispatch($request);
} catch (HttpException $e) {
    $response = new Response($e->status === 404 ? 'Page introuvable' : $e->getMessage(), $e->status, $e->headers);
}

$response->send();
