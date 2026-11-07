<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;

final class HomeController
{
    public function __construct(private readonly Request $request)
    {
    }

    /** GET / : redirige vers la langue préférée du navigateur (français par défaut). */
    public function root(): Response
    {
        return Response::redirect('/' . self::preferredLang($this->request->header('Accept-Language') ?? ''), 302);
    }

    /** GET /fr, /en */
    public function index(string $lang): string
    {
        return View::render('site/home', [
            'title' => t('pages.home'),
        ]);
    }

    /** 'en-US,en;q=0.9,fr;q=0.8' → 'en' ; français si aucune langue connue n'est demandée. */
    public static function preferredLang(string $acceptLanguage): string
    {
        foreach (explode(',', $acceptLanguage) as $part) {
            $code = strtolower(substr(trim($part), 0, 2));
            if ($code === 'fr' || $code === 'en') {
                return $code;
            }
        }

        return 'fr';
    }
}
