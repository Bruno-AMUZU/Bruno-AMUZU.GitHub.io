<?php

declare(strict_types=1);

/*
 * Fonctions utilitaires globales, chargées par Composer (composer.json → autoload.files).
 * Réservé aux petites fonctions appelées très souvent dans les vues.
 */

/**
 * Échappe une valeur avant de l'afficher dans du HTML, pour empêcher l'injection de code (XSS).
 * À utiliser pour toute donnée affichée dans une vue : <?= e($project->title) ?>
 */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
