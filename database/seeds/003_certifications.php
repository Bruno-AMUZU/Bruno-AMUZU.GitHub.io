<?php

declare(strict_types=1);

/*
 * Certifications de la spec (design/content.*.json → certifications), sans PDF :
 * les fichiers seront envoyés depuis le back-office.
 */

return function (PDO $db): void {
    $content = seed_content();
    $months = ['janvier' => 1, 'février' => 2, 'mars' => 3, 'avril' => 4, 'mai' => 5, 'juin' => 6, 'juillet' => 7,
        'août' => 8, 'septembre' => 9, 'octobre' => 10, 'novembre' => 11, 'décembre' => 12];

    foreach ($content['fr']['certifications'] as $i => $fr) {
        $en = $content['en']['certifications'][$i] ?? $fr;
        [$month, $year] = array_pad(explode(' ', mb_strtolower($fr['date'])), 2, '');
        $date = isset($months[$month]) && ctype_digit($year) ? sprintf('%s-%02d-01', $year, $months[$month]) : null;

        $id = seed_insert($db, 'certifications', [
            'obtained_on' => $date,
            'organization' => $fr['org'],
            'position' => $i + 1,
        ]);
        foreach (['fr' => $fr, 'en' => $en] as $lang => $certification) {
            seed_insert_row($db, 'certification_translations', ['certification_id' => $id, 'lang' => $lang, 'title' => $certification['title']]);
        }
    }
};
