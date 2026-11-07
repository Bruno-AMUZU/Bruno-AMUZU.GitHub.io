<?php
/**
 * Balises SEO de la page : description, adresse canonique, versions FR / EN (hreflang),
 * Open Graph (aperçu lors d'un partage sur LinkedIn, X…).
 *
 * @var string      $title
 * @var string|null $description
 * @var string|null $image  adresse de l'image de partage (capture d'un projet…)
 * @var string      $type   'website' | 'article'
 */
$current = App\Core\App::get(App\Core\Request::class)->path;
$alternates = ['fr' => switch_lang_url('fr'), 'en' => switch_lang_url('en')];
$description = trim((string) ($description ?? ''));
?>
<?php if ($description !== ''): ?>
    <meta name="description" content="<?= e(mb_strimwidth($description, 0, 160, '…')) ?>">
<?php endif ?>
<link rel="canonical" href="<?= e(absolute_url($current)) ?>">
<?php foreach ($alternates as $code => $href): ?>
    <link rel="alternate" hreflang="<?= e($code) ?>" href="<?= e(absolute_url($href)) ?>">
<?php endforeach ?>
<link rel="alternate" hreflang="x-default" href="<?= e(absolute_url($alternates['fr'])) ?>">
<meta property="og:site_name" content="Bruno Amuzu">
<meta property="og:title" content="<?= e($title) ?>">
<?php if ($description !== ''): ?>
    <meta property="og:description" content="<?= e(mb_strimwidth($description, 0, 200, '…')) ?>">
<?php endif ?>
<meta property="og:type" content="<?= e($type ?? 'website') ?>">
<meta property="og:url" content="<?= e(absolute_url($current)) ?>">
<meta property="og:locale" content="<?= lang() === 'fr' ? 'fr_FR' : 'en_US' ?>">
<?php if (!empty($image)): ?>
    <meta property="og:image" content="<?= e(absolute_url($image)) ?>">
    <meta name="twitter:card" content="summary_large_image">
<?php else: ?>
    <meta name="twitter:card" content="summary">
<?php endif ?>
