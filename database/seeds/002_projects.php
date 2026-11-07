<?php

declare(strict_types=1);

/*
 * Projets fictifs de la spec (design/content.*.json → projects), publiés, pour développer les pages.
 */

return function (PDO $db): void {
    $content = seed_content();
    $types = ['Site' => 'site', 'Application' => 'app', 'IA' => 'ia'];

    foreach ($content['fr']['projects'] as $i => $fr) {
        $en = $content['en']['projects'][$i] ?? $fr;
        $id = seed_insert($db, 'projects', [
            'slug' => $fr['slug'],
            'year' => (int) $fr['year'],
            'type' => $types[$fr['type']] ?? 'site',
            'status' => 'published',
            'position' => $i + 1,
        ]);

        foreach (['fr' => $fr, 'en' => $en] as $lang => $project) {
            seed_insert_row($db, 'project_translations', [
                'project_id' => $id,
                'lang' => $lang,
                'title' => $project['title'],
                'summary' => $project['summary'],
                'context' => $project['caseStudy']['context'] ?? '',
                'solution' => $project['caseStudy']['solution'] ?? '',
                'result' => $project['caseStudy']['result'] ?? '',
            ]);
        }

        foreach ($fr['stack'] as $position => $name) {
            seed_insert_row($db, 'project_technologies', ['project_id' => $id, 'name' => $name, 'position' => $position]);
        }
    }
};
