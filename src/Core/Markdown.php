<?php

declare(strict_types=1);

namespace App\Core;

use League\CommonMark\CommonMarkConverter;

/**
 * Transforme le contenu Markdown des articles en HTML, sans risque d'injection :
 * le HTML écrit dans le texte est échappé (affiché tel quel) et les liens javascript: / data: sont retirés.
 */
final class Markdown
{
    private CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]);
    }

    public function toHtml(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }
}
