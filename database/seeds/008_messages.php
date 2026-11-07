<?php

declare(strict_types=1);

/*
 * Messages de démonstration : uniquement des textes génériques « [Nom] », « [email] », « [Message] »
 * (design/pages/admin-messages.md : aucun faux message réaliste).
 */

return function (PDO $db): void {
    $demo = [
        ['type' => 'site', 'status' => 'unread', 'ago' => '2 hours'],
        ['type' => 'app', 'status' => 'unread', 'ago' => '1 day'],
        ['type' => 'ia', 'status' => 'read', 'ago' => '3 days'],
        ['type' => 'other', 'status' => 'archived', 'ago' => '10 days'],
    ];
    foreach ($demo as $i => $message) {
        $db->prepare(
            "INSERT INTO messages (name, email, type, body, status, created_at)
             VALUES (:name, :email, :type, :body, :status, now() - CAST(:ago AS interval))"
        )->execute([
            'name' => '[Nom ' . ($i + 1) . ']',
            'email' => 'demo' . ($i + 1) . '@portfolio.test',
            'type' => $message['type'],
            'body' => '[Message]',
            'status' => $message['status'],
            'ago' => $message['ago'],
        ]);
    }
};
