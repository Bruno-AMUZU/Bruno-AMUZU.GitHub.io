<?php

declare(strict_types=1);

/*
 * Transforme design/tokens.json en variables CSS (public/assets/css/tokens.css).
 *   docker compose exec app php bin/tokens.php
 *
 * À relancer après chaque modification de tokens.json. Le fichier généré est commité :
 * le serveur de production n'a pas besoin de le reconstruire.
 */

if (PHP_SAPI !== 'cli') {
    exit("Script réservé à la ligne de commande.\n");
}

$root = dirname(__DIR__);
$tokens = json_decode((string) file_get_contents($root . '/design/tokens.json'), true, flags: JSON_THROW_ON_ERROR);

/** 'invInk' → 'inv-ink', 'display-xl' reste 'display-xl' */
$kebab = fn (string $name): string => strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
$px = fn (int|float $value): string => $value == 0 ? '0' : $value . 'px';

/** '#000000' + 0.22 → 'rgb(0 0 0 / 0.22)' */
$rgba = function (string $hex, float $opacity): string {
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

    return "rgb({$r} {$g} {$b} / {$opacity})";
};

$color = $tokens['color'];
$lines = fn (array $vars, string $indent = '  '): string => implode("\n", array_map(
    fn ($name, $value) => "{$indent}--{$name}: {$value};",
    array_keys($vars),
    $vars,
));

// ── Variables communes aux deux thèmes ──
$common = [
    'color-accent' => $color['accent']['value'],
    'color-on-accent' => $color['onAccent']['value'],
    'color-brand-bg' => $color['brand']['bg']['value'],
    'color-brand-fg' => $color['brand']['fg']['value'],
    'color-success' => $color['status']['success']['value'],
    'color-danger' => $color['status']['danger']['value'],
    'color-overlay' => $rgba($color['overlay']['value'], $color['overlay']['opacity']),
];
foreach ($color['tech'] as $name => $value) {
    if (!str_starts_with($name, '$')) {
        $common['color-tech-' . $kebab($name)] = $value;
    }
}

$fonts = [
    'display' => '"' . $tokens['font']['display']['family'] . '", system-ui, sans-serif',
    'body' => '"' . $tokens['font']['body']['family'] . '", system-ui, sans-serif',
    'mono' => 'ui-monospace, Consolas, Menlo, monospace', // spec : pile monospace système sur le site
];
foreach ($fonts as $name => $stack) {
    $common['font-' . $name] = $stack;
}

$mobileType = [];
foreach ($tokens['type'] as $name => $style) {
    if (str_starts_with($name, '$')) {
        continue;
    }
    $common["type-{$name}-font"] = "var(--font-{$style['font']})";
    $common["type-{$name}-weight"] = (string) $style['weight'];
    $common["type-{$name}-size"] = $px($style['size']);
    $common["type-{$name}-line"] = $px($style['lineHeight']);
    $common["type-{$name}-tracking"] = $style['letterSpacing'] == 0 ? '0' : $style['letterSpacing'] . 'em';
    if (isset($style['mobile'])) {
        $mobileType["type-{$name}-size"] = $px($style['mobile']['size']);
        $mobileType["type-{$name}-line"] = $px($style['mobile']['lineHeight']);
    }
}

foreach ($tokens['space'] as $name => $value) {
    if (!str_starts_with((string) $name, '$')) {
        $common["space-{$name}"] = $px($value);
    }
}
foreach ($tokens['radius'] as $name => $value) {
    $common["radius-{$name}"] = $px($value);
}
$shadow = $tokens['shadow']['float'];
$common['shadow-float'] = sprintf('%s %s %s %s', $px($shadow['x']), $px($shadow['y']), $px($shadow['blur']), $rgba($shadow['color'], $shadow['opacity']));
foreach ($tokens['size'] as $name => $value) {
    $common['size-' . $kebab($name)] = $px($value);
}

$desktop = $tokens['layout']['desktop'];
$mobile = $tokens['layout']['mobile'];
$common['container-max'] = $px($desktop['containerMax']);
$common['gutter'] = $px($desktop['gutter']);
$common['section-gap'] = $px($desktop['sectionGap']);
$common['card-gap'] = $px($desktop['cardGap']);
$mobileLayout = [
    'gutter' => $px($mobile['gutter']),
    'section-gap' => $px($mobile['sectionGap']),
    'card-gap' => $px($mobile['cardGap']),
];

// ── Couleurs propres à chaque thème ──
$theme = function (string $name) use ($color, $kebab): array {
    $vars = [];
    foreach ($color[$name] as $key => $token) {
        $vars['color-' . $kebab($key)] = $token['value'];
    }
    $vars['color-brand-edge'] = $color['brand']['edge'][$name];

    return $vars;
};
$light = $theme('light');
$dark = $theme('dark');
$breakpoint = $tokens['layout']['breakpoint'];

$css = <<<CSS
/*
 * Généré par bin/tokens.php à partir de design/tokens.json — ne pas modifier à la main.
 *
 * Thème : clair par défaut, sombre si le système le demande, sauf choix explicite du visiteur
 * (attribut data-theme="light" ou "dark" sur <html>).
 */

:root {
{$lines($common)}
{$lines($light)}
  color-scheme: light;
}

@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) {
{$lines($dark, '    ')}
    color-scheme: dark;
  }
}

:root[data-theme="dark"] {
{$lines($dark)}
  color-scheme: dark;
}

/* Mobile : sous {$breakpoint} px */
@media (max-width: {$px($breakpoint - 1)}) {
  :root {
{$lines([...$mobileType, ...$mobileLayout], '    ')}
  }
}

CSS;

file_put_contents($root . '/public/assets/css/tokens.css', $css);
echo 'public/assets/css/tokens.css : ' . substr_count($css, '--') . " variables écrites.\n";
