<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\View;

/** Mentions légales (design/pages/mentions-legales.md). Pas de ChatBubble. */
final class LegalController
{
    /** GET /fr/mentions-legales, /en/legal-notice */
    public function show(): string
    {
        return View::render('site/legal', [
            'title' => t('legal.title'),
            'blocks' => t_list('legal.blocks'),
        ]);
    }
}
