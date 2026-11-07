<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\View;

final class HomeController
{
    /** GET /{lang} */
    public function index(string $lang): void
    {
        echo View::render('site/home', [
            'lang' => $lang,
            'title' => $lang === 'fr' ? 'Accueil' : 'Home', // provisoire, en attendant les fichiers lang/
        ]);
    }
}
