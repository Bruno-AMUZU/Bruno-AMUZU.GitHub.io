<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Env;
use App\Core\Response;
use App\Core\Translator;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\ProjectRepository;

/** sitemap.xml et robots.txt, générés à partir des contenus publiés. */
final class SeoController
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly ArticleRepository $articles,
        private readonly Translator $translator,
    ) {
    }

    /** GET /sitemap.xml : chaque page publiée dans les deux langues, avec ses versions alternatives. */
    public function sitemap(): Response
    {
        $pages = [];
        foreach (['home', 'projects', 'lab', 'about', 'contact', 'legal'] as $name) {
            $pages[] = [$name, []];
        }
        foreach ($this->projects->published('fr') as $project) {
            $pages[] = ['project', ['slug' => $project->slug]];
        }
        foreach ($this->articles->published('fr') as $article) {
            $pages[] = ['article', ['slug' => $article->slug]];
        }

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        foreach ($pages as [$name, $params]) {
            if (!has_route($name)) {
                continue;
            }
            $urls = ['fr' => absolute_url(url($name, $params, 'fr')), 'en' => absolute_url(url($name, $params, 'en'))];
            foreach ($urls as $loc) {
                $xml->startElement('url');
                $xml->writeElement('loc', $loc);
                foreach ($urls as $lang => $alternate) {
                    $xml->startElement('xhtml:link');
                    $xml->writeAttribute('rel', 'alternate');
                    $xml->writeAttribute('hreflang', $lang);
                    $xml->writeAttribute('href', $alternate);
                    $xml->endElement();
                }
                $xml->endElement();
            }
        }
        $xml->endElement();

        return new Response($xml->outputMemory(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** GET /robots.txt : tout est indexable en production sauf le back-office ; rien ailleurs (dev, préproduction). */
    public function robots(): Response
    {
        $body = Env::get('APP_ENV') === 'prod'
            ? "User-agent: *\nDisallow: /admin\nDisallow: /uploads/\nAllow: /uploads/*.webp\nAllow: /uploads/*.png\nAllow: /uploads/*.jpg\n\nSitemap: " . absolute_url('/sitemap.xml') . "\n"
            : "User-agent: *\nDisallow: /\n";

        return new Response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
