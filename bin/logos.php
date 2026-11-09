<?php

declare(strict_types=1);

/*
 * Importe les logos de technologies depuis un paquet Devicon (https://devicon.dev) décompressé.
 *
 *   1. Télécharger et décompresser le paquet npm, hors du projet :
 *        curl -L -o devicon.tgz https://registry.npmjs.org/devicon/-/devicon-2.16.0.tgz
 *        tar -xzf devicon.tgz            → dossier package/ (devicon.json + icons/)
 *   2. Le copier dans storage/ (visible depuis le conteneur), puis :
 *        docker compose exec app php bin/logos.php storage/devicon/package
 *   3. Supprimer la copie de storage/.
 *
 * Pour chaque technologie : variante « original » (sinon plain, puis les variantes avec texte),
 * nettoyée et rendue sûre avant d'être écrite dans public/assets/img/tech/{nom}.svg :
 *   - refus des fichiers avec script, gestionnaire d'événement, foreignObject ou lien externe ;
 *   - identifiants préfixés par le nom du logo (plusieurs logos insérés dans une même page ne se marchent pas dessus) ;
 *   - logo entièrement sombre (GitHub…) : ses couleurs deviennent currentColor, il prend la couleur du texte
 *     et reste visible en thème sombre ; logo mêlant noir et couleurs (Next.js…) : intact, sur pastille claire.
 * Écrit aussi public/assets/img/tech/logos.json : nom → ton (mono, dark, color) et noms alternatifs
 * (recherche du back-office).
 */

if ($argc < 2 || !is_file($argv[1] . '/devicon.json')) {
    fwrite(STDERR, "Usage : php bin/logos.php <dossier du paquet Devicon contenant devicon.json>\n");
    exit(1);
}

$source = rtrim($argv[1], '/');
$target = dirname(__DIR__) . '/public/assets/img/tech';
$index = json_decode((string) file_get_contents("{$source}/devicon.json"), true, flags: JSON_THROW_ON_ERROR);

// Noms gardés pour ne pas casser les compétences déjà enregistrées (clé « html », pas « html5 »).
$rename = ['html5' => 'html', 'css3' => 'css'];
$variants = ['original', 'plain', 'original-wordmark', 'plain-wordmark', 'line'];

$catalog = [];
$tones = [];
$written = 0;
$rejected = [];

foreach ($index as $entry) {
    $name = (string) $entry['name'];
    $key = $rename[$name] ?? $name;
    if (!preg_match('/^[a-z0-9-]+$/', $key)) {
        $rejected[] = "{$name} (nom invalide)";
        continue;
    }

    $file = null;
    foreach ($variants as $variant) {
        if (in_array($variant, $entry['versions']['svg'] ?? [], true) && is_file("{$source}/icons/{$name}/{$name}-{$variant}.svg")) {
            $file = "{$source}/icons/{$name}/{$name}-{$variant}.svg";
            break;
        }
    }
    if ($file === null) {
        $rejected[] = "{$name} (aucune variante SVG)";
        continue;
    }

    $clean = clean_svg((string) file_get_contents($file), $key);
    if ($clean === null) {
        $rejected[] = "{$name} (contenu refusé)";
        continue;
    }

    file_put_contents("{$target}/{$key}.svg", $clean['svg']);
    $written++;

    $aliases = array_values(array_unique(array_filter(array_map(
        fn ($alias) => mb_strtolower(trim((string) $alias)),
        [...($entry['altnames'] ?? []), ...($key !== $name ? [$name] : [])],
    ), fn ($alias) => $alias !== '' && $alias !== $key)));
    $catalog[$key] = ['tone' => $clean['tone'], 'aliases' => $aliases];
    $tones[$clean['tone']] = ($tones[$clean['tone']] ?? 0) + 1;
}

ksort($catalog);
file_put_contents("{$target}/logos.json", json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

echo "{$written} logos écrits dans public/assets/img/tech/ ("
    . implode(', ', array_map(fn ($tone, $count) => "{$count} {$tone}", array_keys($tones), $tones)) . ").\n";
if ($rejected !== []) {
    echo count($rejected) . " ignorés : " . implode(', ', $rejected) . "\n";
}

/**
 * SVG nettoyé et rendu sûr avec son ton, ou null s'il contient quelque chose de dangereux.
 *
 * @return array{svg: string, tone: string}|null
 */
function clean_svg(string $raw, string $key): ?array
{
    if (preg_match('/<!DOCTYPE|<!ENTITY/i', $raw)) {
        return null;
    }

    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    if (!@$doc->loadXML($raw, LIBXML_NONET)) {
        return null;
    }
    $svg = $doc->documentElement;
    if ($svg === null || $svg->localName !== 'svg') {
        return null;
    }

    $xpath = new DOMXPath($doc);

    // Refus : scripts, contenu HTML embarqué, gestionnaires d'événements, liens hors du fichier.
    if ($xpath->query('//*[local-name()="script" or local-name()="foreignObject"]')->length > 0) {
        return null;
    }
    foreach ($xpath->query('//@*') as $attribute) {
        $attrName = strtolower($attribute->nodeName);
        $value = trim($attribute->nodeValue ?? '');
        if (str_starts_with($attrName, 'on')) {
            return null;
        }
        if (in_array($attrName, ['href', 'xlink:href'], true) && !str_starts_with($value, '#')) {
            return null;
        }
        if (preg_match('/javascript:|url\(\s*[\'"]?(?!#)/i', $value)) {
            return null;
        }
    }

    // Commentaires et métadonnées inutiles.
    foreach (iterator_to_array($xpath->query('//comment() | //*[local-name()="metadata" or local-name()="title" or local-name()="desc"]')) as $node) {
        $node->parentNode?->removeChild($node);
    }

    // Taille fixée par la page, pas par le fichier.
    if (!$svg->hasAttribute('viewBox') && $svg->hasAttribute('width') && $svg->hasAttribute('height')) {
        $svg->setAttribute('viewBox', sprintf('0 0 %s %s', (float) $svg->getAttribute('width'), (float) $svg->getAttribute('height')));
    }
    $svg->removeAttribute('width');
    $svg->removeAttribute('height');

    // Identifiants préfixés : « a » → « php-a », et toutes les références avec.
    $ids = [];
    foreach ($xpath->query('//*[@id]') as $element) {
        $old = $element->getAttribute('id');
        $ids[$old] = "{$key}-{$old}";
        $element->setAttribute('id', $ids[$old]);
    }
    if ($ids !== []) {
        $replaceRefs = static function (string $text) use ($ids): string {
            return preg_replace_callback('/(url\(\s*#|^#)([^\s)\'"]+)/', fn ($m) => $m[1] . ($ids[$m[2]] ?? $m[2]), $text) ?? $text;
        };
        foreach ($xpath->query('//@*') as $attribute) {
            $attribute->value = $replaceRefs($attribute->value);
        }
        foreach ($xpath->query('//*[local-name()="style"]') as $style) {
            $style->textContent = $replaceRefs($style->textContent);
        }
    }

    // Ton du logo, pour le thème sombre :
    //   mono  = uniquement des couleurs très sombres → elles deviennent currentColor (couleur du texte) ;
    //   dark  = du noir mêlé à d'autres couleurs → logo intact, posé sur une pastille claire en thème sombre ;
    //   color = aucune couleur sombre → rien à faire.
    $defaultFill = has_default_fill($xpath);
    [$hasDark, $hasOther] = paint_summary($xpath);
    $hasDark = $hasDark || $defaultFill;
    $tone = !$hasDark ? 'color' : ($hasOther ? 'dark' : 'mono');

    if ($tone === 'mono') {
        foreach ($xpath->query('//*') as $element) {
            foreach (['fill', 'stroke', 'stop-color', 'color'] as $prop) {
                if ($element->hasAttribute($prop) && is_dark($element->getAttribute($prop))) {
                    $element->setAttribute($prop, 'currentColor');
                }
            }
            if ($element->hasAttribute('style')) {
                $element->setAttribute('style', darken_to_current($element->getAttribute('style')));
            }
        }
        foreach ($xpath->query('//*[local-name()="style"]') as $style) {
            $style->textContent = darken_to_current($style->textContent);
        }
        // Formes sans couleur dans leurs ancêtres : noires par défaut → currentColor sur le <svg>.
        if ($defaultFill && !$svg->hasAttribute('fill')) {
            $svg->setAttribute('fill', 'currentColor');
        }
    }

    $out = $doc->saveXML($svg);

    return $out === false ? null : ['svg' => $out . "\n", 'tone' => $tone];
}

/**
 * Couleurs de peinture du logo (attributs, styles, balises <style>).
 *
 * @return array{0: bool, 1: bool} [contient du très sombre, contient une autre couleur (dégradé compris)]
 */
function paint_summary(DOMXPath $xpath): array
{
    $values = [];
    foreach ($xpath->query('//@fill | //@stroke | //@stop-color') as $attribute) {
        $values[] = $attribute->value;
    }
    $css = '';
    foreach ($xpath->query('//@style') as $attribute) {
        $css .= ';' . $attribute->value;
    }
    foreach ($xpath->query('//*[local-name()="style"]') as $style) {
        $css .= ';' . $style->textContent;
    }
    preg_match_all('/\b(?:fill|stroke|stop-color)\s*:\s*([^;}"\']+)/i', $css, $m);
    $values = [...$values, ...$m[1]];

    $dark = false;
    $other = false;
    foreach ($values as $value) {
        $value = strtolower(trim($value));
        if (in_array($value, ['none', 'transparent', 'inherit', 'currentcolor', ''], true)) {
            continue;
        }
        if (is_dark($value)) {
            $dark = true;
        } else {
            $other = true; // couleur claire ou vive, dégradé url(#…), etc.
        }
    }

    return [$dark, $other];
}

function is_dark(string $color): bool
{
    $color = strtolower(trim($color));
    if ($color === 'black') {
        return true;
    }
    if (preg_match('/^#([0-9a-f]{3})$/', $color, $m)) {
        $color = '#' . $m[1][0] . $m[1][0] . $m[1][1] . $m[1][1] . $m[1][2] . $m[1][2];
    }
    if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/', $color, $m)) {
        [$r, $g, $b] = [hexdec($m[1]), hexdec($m[2]), hexdec($m[3])];
    } elseif (preg_match('/^rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/', $color, $m)) {
        [$r, $g, $b] = [(int) $m[1], (int) $m[2], (int) $m[3]];
    } else {
        return false;
    }
    $luminance = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;

    return $luminance < 0.15; // noir et gris très foncés : invisibles sur le fond sombre du site
}

/** Dans un style CSS (attribut style ou balise <style>), remplace les couleurs sombres par currentColor. */
function darken_to_current(string $css): string
{
    return preg_replace_callback(
        '/\b(fill|stroke|stop-color|color)\s*:\s*([^;}"\']+)/i',
        fn ($m) => is_dark($m[2]) ? "{$m[1]}:currentColor" : $m[0],
        $css,
    ) ?? $css;
}

/** Une forme hérite-t-elle de la couleur par défaut (noir), faute de remplissage sur elle ou ses ancêtres ? */
function has_default_fill(DOMXPath $xpath): bool
{
    // Les formes dans <defs>, <clipPath>, <mask> ou <pattern> ne sont pas peintes directement.
    $shapes = $xpath->query('//*[(local-name()="path" or local-name()="rect" or local-name()="circle" or local-name()="ellipse"
        or local-name()="polygon" or local-name()="polyline" or local-name()="text")
        and not(ancestor::*[local-name()="defs" or local-name()="clipPath" or local-name()="mask" or local-name()="pattern"])]');
    foreach ($shapes as $shape) {
        $node = $shape;
        $filled = false;
        while ($node instanceof DOMElement) {
            if ($node->hasAttribute('fill') || preg_match('/(^|;)\s*fill\s*:/', $node->getAttribute('style')) || $node->hasAttribute('class')) {
                $filled = true; // une classe peut définir le remplissage dans <style> : on ne touche pas
                break;
            }
            $node = $node->parentNode;
        }
        if (!$filled) {
            return true;
        }
    }

    return false;
}
