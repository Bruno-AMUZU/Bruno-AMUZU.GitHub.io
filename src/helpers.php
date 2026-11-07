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

/**
 * Ancienne valeur d'un champ après un formulaire en erreur, sinon $default.
 * <input name="email" value="<?= e(old('email')) ?>">
 */
function old(string $field, string $default = ''): string
{
    $value = App\Core\App::get(App\Core\Session::class)->getFlash('old', [])[$field] ?? $default;

    return is_string($value) ? $value : $default;
}

/** Champ caché contenant le jeton CSRF, à placer dans chaque formulaire POST. */
function csrf_field(): string
{
    $token = App\Core\App::get(App\Core\Csrf::class)->token();

    return '<input type="hidden" name="' . App\Core\Csrf::FIELD . '" value="' . e($token) . '">';
}

/** Clé du message d'erreur d'un champ après un formulaire en erreur, ou null. */
function error(string $field): ?string
{
    return App\Core\App::get(App\Core\Session::class)->getFlash('errors', [])[$field] ?? null;
}
