<?php

declare(strict_types=1);

/*
 * Articles de démonstration du Lab (design/content.*.json → lab) : aucun sujet n'est encore fixé
 * (question ouverte de DESIGN.md), ils gardent donc leurs « [À FOURNIR] ». Le contenu reprend les
 * textes de démonstration de la page article (intertitre, code, citation) pour montrer la mise en forme.
 */

return function (PDO $db): void {
    $content = seed_content();
    $categories = ['Context engineering' => 'ia', 'Harness engineering' => 'ia', 'RAG' => 'ia']; // sujets IA de la spec

    $body = function (array $page): string {
        return implode("\n\n", [
            $page['sample'],
            '## ' . $page['sampleHeading'],
            $page['sample'],
            "```php\n// " . $page['sampleCode'] . "\n\$context = \$retriever->search(\$question, limit: 5);\n```",
            '> ' . $page['sampleQuote'],
        ]);
    };

    foreach ($content['fr']['lab'] as $i => $fr) {
        $en = $content['en']['lab'][$i] ?? $fr;
        $id = seed_insert($db, 'articles', [
            'slug' => 'article-demo-' . ($i + 1),
            'category' => $categories[$fr['category']] ?? 'ia',
            'status' => 'published',
            'position' => $i + 1,
        ]);
        foreach (['fr' => [$fr, $content['fr']['articlePage']], 'en' => [$en, $content['en']['articlePage']]] as $lang => [$article, $page]) {
            seed_insert_row($db, 'article_translations', [
                'article_id' => $id,
                'lang' => $lang,
                'title' => $article['title'] . ' ' . ($i + 1),
                'excerpt' => $page['sample'],
                'content' => $body($page),
            ]);
        }
    }
};
