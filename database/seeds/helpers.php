<?php

declare(strict_types=1);

/*
 * Outils partagés par les seeds. Ce fichier n'est pas un seed : bin/seed.php n'exécute que
 * les fichiers numérotés (NNN_nom.php).
 */

/**
 * Textes de démonstration FR et EN, lus dans la spec (design/content.*.json).
 *
 * @return array{fr: array<string, mixed>, en: array<string, mixed>}
 */
function seed_content(): array
{
    static $content = null;

    return $content ??= [
        'fr' => json_decode((string) file_get_contents(ROOT_PATH . '/design/content.fr.json'), true, flags: JSON_THROW_ON_ERROR),
        'en' => json_decode((string) file_get_contents(ROOT_PATH . '/design/content.en.json'), true, flags: JSON_THROW_ON_ERROR),
    ];
}

/** Insère une ligne et renvoie son id. */
function seed_insert(PDO $db, string $table, array $row): int
{
    $columns = array_keys($row);
    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s) RETURNING id',
        $table,
        implode(', ', $columns),
        implode(', ', array_map(fn ($c) => ':' . $c, $columns)),
    );
    $stmt = $db->prepare($sql);
    $stmt->execute($row);

    return (int) $stmt->fetchColumn();
}

/** Insère une ligne sans colonne id (tables de traduction, liaisons). */
function seed_insert_row(PDO $db, string $table, array $row): void
{
    $columns = array_keys($row);
    $db->prepare(sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', array_map(fn ($c) => ':' . $c, $columns)),
    ))->execute($row);
}
