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

/**
 * Réglage du site (e-mail de contact, liens réseaux…). Valeur par défaut tant que le réglage
 * n'est pas enregistré depuis le back-office.
 */
function setting(string $key, string $default = ''): string
{
    $class = 'App\Model\Repository\SettingRepository';
    if (!class_exists($class)) {
        return $default;
    }
    $value = App\Core\App::get($class)->get($key);

    return $value !== null && $value !== '' ? $value : $default;
}

/** « Atelier Brume & Cie » → « atelier-brume-cie » (adresse d'une page). */
function slugify(string $text): string
{
    // Table de translittération explicite : ne dépend pas de la bibliothèque iconv du système.
    static $map = [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ì' => 'i',
        'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ø' => 'o', 'œ' => 'oe',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u', 'ÿ' => 'y', 'ñ' => 'n', 'ß' => 'ss',
    ];
    $text = strtr(mb_strtolower(mb_scrub($text, 'UTF-8'), 'UTF-8'), $map);
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', $text), '-');

    return rtrim(substr($slug, 0, 80), '-');
}

/** Date lisible : « 7 nov. 2026 » / « Nov 7, 2026 ». */
function format_date(?string $date, ?string $lang = null, string $pattern = 'medium'): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $lang ??= lang();
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    $months = $lang === 'fr'
        ? ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.']
        : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $longMonths = $lang === 'fr'
        ? ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre']
        : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $day = (int) date('j', $timestamp);
    $month = (int) date('n', $timestamp) - 1;
    $year = date('Y', $timestamp);

    return match ($pattern) {
        'month' => ($lang === 'fr' ? $longMonths[$month] : $longMonths[$month]) . ' ' . $year,
        default => $lang === 'fr' ? "{$day} {$months[$month]} {$year}" : "{$months[$month]} {$day}, {$year}",
    };
}

/** « 7 nov. 2026 · 6 min » : date et temps de lecture d'un article du Lab. */
function article_meta(App\Model\Entity\Article $article): string
{
    return implode(' · ', array_filter([
        format_date($article->publishedOn),
        $article->readingTime !== null ? $article->readingTime . ' min' : null,
    ]));
}

/** Nombre de messages non lus (compteur de la barre latérale du back-office). */
function admin_unread_count(): int
{
    $class = 'App\Model\Repository\MessageRepository';

    return class_exists($class) ? App\Core\App::get($class)->countUnread() : 0;
}

/** Adresse complète (https://brunoamuzu.dev/fr/projets) à partir d'un chemin, pour le SEO et les e-mails. */
function absolute_url(string $path): string
{
    if (preg_match('#^https?://#', $path)) {
        return $path; // déjà complète (miniature YouTube…)
    }

    return rtrim((string) App\Core\Env::get('APP_URL', ''), '/') . '/' . ltrim($path, '/');
}

/**
 * Adresse d'un fichier de public/assets avec sa version (date de modification) :
 * le navigateur peut le garder en cache un an, et recharge dès que le fichier change.
 */
function asset(string $path): string
{
    $file = ROOT_PATH . '/public/' . ltrim($path, '/');

    return '/' . ltrim($path, '/') . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/** La page existe-t-elle ? Pour ne pas afficher de lien vers une page pas encore construite. */
function has_route(string $name): bool
{
    return App\Core\App::get(App\Core\UrlGenerator::class)->has($name);
}

/** La page en cours dans l'autre langue (sélecteur FR · EN). */
function switch_lang_url(string $lang): string
{
    return App\Core\App::get(App\Core\UrlGenerator::class)->switchTo($lang);
}

/**
 * Affiche un composant de views/components/ : <?= component('button', ['label' => 'Envoyer']) ?>
 * Les propriétés deviennent des variables dans le composant.
 *
 * @param array<string, mixed> $props
 */
function component(string $name, array $props = []): string
{
    return App\Core\View::partial('components/' . $name, ['props' => $props, ...$props]);
}

/**
 * Attributs HTML échappés à partir d'un tableau : attrs(['href' => $url, 'hidden' => true]).
 * true = attribut sans valeur, false / null = attribut absent.
 *
 * @param array<string, string|int|bool|null> $attributes
 */
function attrs(array $attributes): string
{
    $html = '';
    foreach ($attributes as $name => $value) {
        if ($value === null || $value === false) {
            continue;
        }
        if (!preg_match('/^[a-zA-Z_:][a-zA-Z0-9_:.-]*$/', (string) $name)) {
            throw new InvalidArgumentException("Nom d'attribut invalide : {$name}");
        }
        $html .= $value === true ? " {$name}" : ' ' . $name . '="' . e($value) . '"';
    }

    return $html;
}

/** Classes CSS à partir d'une liste, les valeurs vides étant ignorées. */
function classes(string|false|null ...$names): string
{
    return implode(' ', array_filter($names, fn ($n) => is_string($n) && $n !== ''));
}

/**
 * Icône dessinée sur mesure (views/components/icons/), en SVG dans la page, couleur = texte autour.
 * Sans $label, l'icône est décorative (ignorée par les lecteurs d'écran) : le libellé est porté par le bouton.
 */
function icon(string $name, ?string $label = null, int $size = 20): string
{
    static $cache = [];
    if (!preg_match('/^[a-z-]+$/', $name)) {
        throw new InvalidArgumentException("Nom d'icône invalide : {$name}");
    }
    $svg = $cache[$name] ??= trim((string) file_get_contents(ROOT_PATH . "/views/components/icons/{$name}.svg"));

    $attributes = sprintf(' class="icon" width="%1$d" height="%1$d" focusable="false"', $size)
        . ($label === null ? ' aria-hidden="true"' : ' role="img" aria-label="' . e($label) . '"');

    return preg_replace('/^<svg/', '<svg' . $attributes, $svg, 1) ?? $svg;
}

/**
 * Logo officiel d'une techno (Devicon), pour les TechChip.
 * Le logo GitHub est intégré à la page pour prendre la couleur du texte (lisible en thème sombre).
 */
function tech_logo(string $tech, int $size = 24): string
{
    if (!preg_match('/^[a-z0-9-]+$/', $tech) || !is_file(ROOT_PATH . "/public/assets/img/tech/{$tech}.svg")) {
        return '';
    }
    if ($tech === 'github') {
        $svg = (string) file_get_contents(ROOT_PATH . '/public/assets/img/tech/github.svg');

        return preg_replace('/^<svg/', sprintf('<svg width="%1$d" height="%1$d" aria-hidden="true" focusable="false"', $size), $svg, 1) ?? '';
    }

    return sprintf('<img src="/assets/img/tech/%s.svg" width="%2$d" height="%2$d" alt="">', $tech, $size);
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
