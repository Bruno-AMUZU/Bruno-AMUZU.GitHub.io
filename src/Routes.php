<?php

declare(strict_types=1);

namespace App;

use App\Controller\Site\HomeController;
use App\Controller\Site\UploadController;
use App\Core\Router;

/**
 * Toutes les URL du site, au même endroit.
 * Les pages publiques ont un chemin par langue ; le nom (« home », « projects »…) sert à fabriquer les liens.
 */
final class Routes
{
    public static function register(Router $router): void
    {
        $router->get('/', [HomeController::class, 'root']);
        $router->get('/uploads/{year:\d{4}}/{month:\d{2}}/{name:[a-f0-9]{32}\.[a-z0-9]+}', [UploadController::class, 'show']);

        $router->page('home', ['fr' => '/fr', 'en' => '/en'], [HomeController::class, 'index']);
    }
}
