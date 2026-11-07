<?php

declare(strict_types=1);

/*
 * Démarrage des tests : même base que public/index.php (conteneur, routes), sans requête HTTP réelle.
 */

use App\Core\App;
use App\Core\Router;
use App\Core\Services;
use App\Routes;

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

$container = Services::container();
App::boot($container);
Routes::register(new Router($container)); // les vues fabriquent des liens par nom de page
