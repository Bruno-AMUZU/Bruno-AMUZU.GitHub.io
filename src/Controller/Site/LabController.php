<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Markdown;
use App\Core\Request;
use App\Core\View;
use App\Model\Entity\Article;
use App\Model\Repository\ArticleRepository;

/** Lab (design/pages/lab.md, article.md). */
final class LabController
{
    public function __construct(
        private readonly Request $request,
        private readonly ArticleRepository $articles,
        private readonly Markdown $markdown,
        private readonly Auth $auth,
    ) {
    }

    /** GET /fr/lab, /en/lab ; ?categorie=context|harness|rag filtre la grille. */
    public function index(string $lang): string
    {
        $category = $this->request->query('categorie');
        $category = in_array($category, Article::CATEGORIES, true) ? $category : null;

        $labels = t_list('labPage.filters'); // Tout, Context engineering, Harness engineering, RAG
        $filters = [['label' => $labels[0] ?? 'Tout', 'href' => url('lab'), 'active' => $category === null]];
        foreach (Article::CATEGORIES as $i => $key) {
            $filters[] = ['label' => $labels[$i + 1] ?? $key, 'href' => url('lab') . '?categorie=' . $key, 'active' => $category === $key];
        }

        return View::render('site/lab', [
            'title' => t('labPage.title'),
            'description' => t('labPage.intro'),
            'articles' => $this->articles->published($lang, $category),
            'filters' => $filters,
        ]);
    }

    /** GET /fr/lab/{slug}, /en/lab/{slug} ; ?apercu=1 montre un brouillon à un admin connecté. */
    public function show(string $lang, string $slug): string
    {
        $preview = $this->request->query('apercu') === '1' && $this->auth->check();
        $article = $this->articles->findBySlug($slug, $lang, withDrafts: $preview) ?? throw new HttpException(404);

        return View::render('site/article', [
            'title' => $article->title,
            'description' => $article->excerpt,
            'article' => $article,
            'html' => $this->markdown->toHtml($article->content),
            'related' => $this->articles->related($article, $lang),
            'preview' => !$article->isPublished(),
            'ogType' => 'article',
            'image' => $article->youtubeId() !== null ? 'https://i.ytimg.com/vi/' . $article->youtubeId() . '/hqdefault.jpg' : null,
            'scripts' => $article->youtubeId() !== null ? ['/assets/js/video.js'] : [],
        ]);
    }
}
