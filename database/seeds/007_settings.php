<?php

declare(strict_types=1);

/*
 * Réglages de départ : coordonnées et liens de la spec (common.footer.links), disponibilité activée.
 * Les textes du hero restent vides : le site affiche alors ceux de lang/*.php.
 */

return function (PDO $db): void {
    $links = [];
    foreach (seed_content()['fr']['common']['footer']['links'] as $link) {
        $links[$link['label']] = $link['url'];
    }

    $stmt = $db->prepare('INSERT INTO settings (key, value) VALUES (:key, :value) ON CONFLICT (key) DO NOTHING');
    foreach ([
        'available' => '1',
        'contact_email' => 'amuzuelbruno@gmail.com',
        'github_url' => $links['GitHub'] ?? '',
        'linkedin_url' => $links['LinkedIn'] ?? '',
        'x_url' => $links['X'] ?? '',
    ] as $key => $value) {
        $stmt->execute(['key' => $key, 'value' => $value]);
    }
};
