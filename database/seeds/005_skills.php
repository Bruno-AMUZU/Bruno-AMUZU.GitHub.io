<?php

declare(strict_types=1);

/*
 * Stack de la spec (design/content.*.json → stack) : 4 catégories, dans l'ordre des cartes.
 */

return function (PDO $db): void {
    $content = seed_content();
    $categories = ['front', 'back', 'tools', 'ai'];
    $position = 0;

    foreach ($content['fr']['stack'] as $c => $group) {
        foreach ($group['items'] as $i => $item) {
            $en = $content['en']['stack'][$c]['items'][$i] ?? $item;
            $id = seed_insert($db, 'skills', [
                'category' => $categories[$c] ?? 'tools',
                'tech' => $item['tech'] !== 'accent' ? $item['tech'] : null, // « accent » = pas de logo officiel
                'mono' => $item['mono'] ?? null,
                'position' => ++$position,
            ]);
            foreach (['fr' => $item, 'en' => $en] as $lang => $skill) {
                seed_insert_row($db, 'skill_translations', ['skill_id' => $id, 'lang' => $lang, 'name' => $skill['name']]);
            }
        }
    }
};
