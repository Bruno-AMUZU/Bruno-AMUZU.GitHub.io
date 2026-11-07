<?php

declare(strict_types=1);

/*
 * Parcours de la spec (design/content.*.json → timeline). Dates et intitulés exacts : à fournir
 * (question ouverte de DESIGN.md), les entrées gardent donc leurs « [À FOURNIR] ».
 */

return function (PDO $db): void {
    $content = seed_content();
    $kinds = ['Formation' => 'education', 'Expérience' => 'experience'];

    foreach ($content['fr']['timeline'] as $i => $fr) {
        $en = $content['en']['timeline'][$i] ?? $fr;
        $id = seed_insert($db, 'timeline_entries', [
            'kind' => $kinds[$fr['kind']] ?? 'experience',
            'organization' => $fr['org'],
            'place' => $fr['place'],
            'position' => $i + 1,
        ]);
        foreach (['fr' => $fr, 'en' => $en] as $lang => $entry) {
            seed_insert_row($db, 'timeline_entry_translations', [
                'timeline_entry_id' => $id,
                'lang' => $lang,
                'title' => $entry['title'],
                'description' => $entry['text'],
            ]);
        }
    }
};
