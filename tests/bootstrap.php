<?php

declare(strict_types=1);

/*
 * Démarrage des tests : même base que public/index.php, sans requête HTTP réelle.
 */

use App\Core\App;
use App\Core\Services;

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

App::boot(Services::container());
