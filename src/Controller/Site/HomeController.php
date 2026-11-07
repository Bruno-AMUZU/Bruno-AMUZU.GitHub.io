<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\ProjectRepository;
use App\Model\Repository\ServiceRepository;
use App\Model\Repository\SkillRepository;

/** Accueil (design/pages/accueil.md). */
final class HomeController
{
    public function __construct(
        private readonly Request $request,
        private readonly ServiceRepository $services,
        private readonly SkillRepository $skills,
        private readonly ProjectRepository $projects,
        private readonly ArticleRepository $articles,
    ) {
    }

    /** GET / : redirige vers la langue préférée du navigateur (français par défaut). */
    public function root(): Response
    {
        return Response::redirect('/' . self::preferredLang($this->request->header('Accept-Language') ?? ''), 302);
    }

    /** GET /fr, /en */
    public function index(string $lang): string
    {
        // Textes du hero : réglages du back-office, sinon textes par défaut de lang/*.php.
        $hero = [];
        foreach (['status', 'title', 'subtitle'] as $key) {
            $hero[$key] = setting("hero_{$key}.{$lang}", t("home.hero.{$key}"));
        }

        return View::render('site/home', [
            'title' => t('pages.home'),
            'description' => $hero['title'],
            'hero' => $hero,
            'available' => setting('available', '1') === '1',
            'services' => $this->services->all($lang),
            'stack' => $this->skills->byCategory($lang),
            'projects' => $this->projects->published($lang, limit: 3),
            'articles' => $this->articles->published($lang, limit: 3),
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
