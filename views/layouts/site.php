<?php
/**
 * Gabarit commun du site public.
 *
 * @var string $title    titre de la page
 * @var string $content  HTML produit par la vue (déjà échappé par elle)
 * @var list<string> $styles feuilles de style propres à la page (facultatif)
 */
?>
<!DOCTYPE html>
<html lang="<?= e(lang()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Bruno Amuzu</title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/assets/img/favicon-32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
    <link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/space-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/cards.css">
    <?php foreach ($styles ?? [] as $style): ?>
        <link rel="stylesheet" href="<?= e($style) ?>">
    <?php endforeach ?>
    <script nonce="<?= e(csp_nonce()) ?>">
        // Thème choisi par le visiteur, appliqué avant l'affichage pour éviter un flash de l'autre thème.
        try { const t = localStorage.getItem('theme'); if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t; } catch (e) {}
    </script>
    <script src="/assets/js/theme.js" defer></script>
    <script src="/assets/js/menu.js" defer></script>
</head>
<body>
    <a class="skip-link t-label" href="#contenu"><?= e(t('common.skip')) ?></a>
    <div class="page">
        <?= component('header') ?>
        <main id="contenu" class="page-main">
            <?= $content ?>
        </main>
        <?= component('footer') ?>
    </div>
</body>
</html>
