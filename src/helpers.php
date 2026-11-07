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

/**
 * Texte de l'interface dans la langue de la page : <?= e(t('home.hero.title')) ?>
 *
 * @param array<string, string|int> $replace remplace {nom} dans le texte
 */
function t(string $key, array $replace = []): string
{
    return App\Core\App::get(App\Core\Translator::class)->get($key, $replace);
}

/** Liste ou groupe de textes (ex. les filtres d'une page) ; [] si absent. */
function t_list(string $key): array
{
    $value = App\Core\App::get(App\Core\Translator::class)->raw($key);

    return is_array($value) ? $value : [];
}

/** Langue de la page en cours : 'fr' ou 'en'. */
function lang(): string
{
    return App\Core\App::get(App\Core\Translator::class)->lang();
}

/**
 * Lien vers une page par son nom : <a href="<?= e(url('project', ['slug' => $project->slug])) ?>">
 *
 * @param array<string, string|int> $params
 */
function url(string $name, array $params = [], ?string $lang = null): string
{
    return App\Core\App::get(App\Core\UrlGenerator::class)->to($name, $params, $lang);
}

/** La page en cours dans l'autre langue (sélecteur FR · EN). */
function switch_lang_url(string $lang): string
{
    return App\Core\App::get(App\Core\UrlGenerator::class)->switchTo($lang);
}

/** Champ caché contenant le jeton CSRF, à placer dans chaque formulaire POST. */
function csrf_field(): string
{
    $token = App\Core\App::get(App\Core\Csrf::class)->token();

    return '<input type="hidden" name="' . App\Core\Csrf::FIELD . '" value="' . e($token) . '">';
}

/** Nonce CSP de la requête, pour autoriser un <script> écrit dans la page. */
function csp_nonce(): string
{
    return App\Core\App::get(App\Core\SecurityHeaders::class)->nonce();
}

/** Clé du message d'erreur d'un champ après un formulaire en erreur, ou null. */
function error(string $field): ?string
{
    return App\Core\App::get(App\Core\Session::class)->getFlash('errors', [])[$field] ?? null;
}
