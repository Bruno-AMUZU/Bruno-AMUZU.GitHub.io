<?php
/**
 * Gabarit commun du site public.
 *
 * @var string $lang     'fr' ou 'en'
 * @var string $title    titre de la page
 * @var string $content  HTML produit par la vue (déjà échappé par elle)
 */
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Bruno Amuzu</title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/assets/img/favicon-32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
</head>
<body>
    <?php /* Header : composant à venir */ ?>
    <main>
        <?= $content ?>
    </main>
    <?php /* Footer : composant à venir */ ?>
</body>
</html>
