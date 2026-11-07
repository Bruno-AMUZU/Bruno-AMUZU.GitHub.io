<?php
/**
 * Gabarit des écrans de connexion du back-office : une carte centrée (design/pages/admin-connexion.md).
 *
 * @var string $title
 * @var string $content
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
    <link rel="stylesheet" href="<?= e(asset('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/css/admin.css')) ?>">
    <script nonce="<?= e(csp_nonce()) ?>">
        try { const t = localStorage.getItem('theme'); if (t === 'light' || t === 'dark') document.documentElement.dataset.theme = t; } catch (e) {}
    </script>
</head>
<body class="auth-page">
    <main class="auth-card">
        <?= component('marque', ['href' => '/admin/connexion']) ?>
        <?= $content ?>
    </main>
</body>
</html>
