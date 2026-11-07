<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Env;
use App\Core\HttpException;
use App\Core\View;

/**
 * Galerie des composants, pour les vérifier à l'œil dans les deux thèmes.
 * Disponible uniquement en développement (APP_ENV=dev) : /dev/composants
 */
final class StyleguideController
{
    public function index(): string
    {
        if (Env::get('APP_ENV') !== 'dev') {
            throw new HttpException(404);
        }

        return View::render('dev/styleguide', ['title' => 'Composants', 'styles' => ['/assets/css/dev.css']]);
    }
}
