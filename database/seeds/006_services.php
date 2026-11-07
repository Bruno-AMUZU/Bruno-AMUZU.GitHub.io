<?php

declare(strict_types=1);

/*
 * Services de la spec (design/content.*.json → home.services.items).
 */

return function (PDO $db): void {
    $content = seed_content();

    foreach ($content['fr']['home']['services']['items'] as $i => $fr) {
        $en = $content['en']['home']['services']['items'][$i] ?? $fr;
        $id = seed_insert($db, 'services', ['illustration' => $fr['illustration'], 'position' => $i + 1]);
        foreach (['fr' => $fr, 'en' => $en] as $lang => $service) {
            seed_insert_row($db, 'service_translations', [
                'service_id' => $id,
                'lang' => $lang,
                'title' => $service['title'],
                'description' => $service['text'],
            ]);
        }
    }
};
