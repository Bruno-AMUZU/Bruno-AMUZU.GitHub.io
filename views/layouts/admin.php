<?php
/**
 * Gabarit du back-office : AdminSidebar + zone principale (design/pages/admin-tableau-de-bord.md → Mise en page).
 * Français uniquement, pas de ChatBubble.
 *
 * @var string $title
 * @var string $content
 * @var int    $unread
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> · Back-office</title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/space-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script nonce="<?= e(csp_nonce()) ?>">
        try { const t = localStorage.getItem('theme'); if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t; } catch (e) {}
    </script>
    <script src="/assets/js/admin.js" defer></script>
</head>
<body class="admin">
    <a class="skip-link t-label" href="#contenu"><?= e(t('common.skip')) ?></a>
    <?= component('admin-sidebar', ['unread' => $unread ?? admin_unread_count()]) ?>
    <main class="admin-main" id="contenu">
        <?php $flash = App\Core\App::get(App\Core\Session::class)->getFlash('notice') ?>
        <?php if ($flash): ?>
            <p class="admin-flash t-small" role="status"><?= e($flash) ?></p>
        <?php endif ?>
        <?= $content ?>
    </main>
</body>
</html>
