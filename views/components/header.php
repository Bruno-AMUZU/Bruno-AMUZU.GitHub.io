<?php
/**
 * Header — barre en pilule du site public (components.md → Header).
 *
 * Desktop : Marque · liens · FR · EN · thème · « Me contacter ».
 * Mobile : Marque · thème · menu ; le menu ouvre un panneau plein écran (assets/js/menu.js)
 * avec les mêmes liens, le sélecteur de langue et le bouton de contact.
 */
$home = url('home');
$current = App\Core\App::get(App\Core\UrlGenerator::class)->currentName();

// Une entrée n'apparaît que si sa page existe déjà ; Services et Stack sont des sections de l'accueil.
$items = array_filter([
    ['label' => t('common.nav.services'), 'href' => $home . '#services', 'name' => null],
    ['label' => t('common.nav.stack'), 'href' => $home . '#stack', 'name' => null],
    has_route('projects') ? ['label' => t('common.nav.projects'), 'href' => url('projects'), 'name' => 'projects'] : null,
    has_route('lab') ? ['label' => t('common.nav.lab'), 'href' => url('lab'), 'name' => 'lab'] : null,
    has_route('about') ? ['label' => t('common.nav.about'), 'href' => url('about'), 'name' => 'about'] : null,
]);
$isActive = fn (array $item): bool => $item['name'] !== null && ($current === $item['name'] || str_starts_with((string) $current, $item['name'] . '.'));
$contact = has_route('contact') ? url('contact') : 'mailto:' . setting('contact_email', 'amuzuelbruno@gmail.com');
?>
<header class="site-header container">
    <div class="site-header__bar">
        <?= component('marque') ?>

        <nav class="site-header__nav desktop-only" aria-label="<?= e(lang() === 'fr' ? 'Navigation principale' : 'Main navigation') ?>">
            <ul class="site-header__links" role="list">
                <?php foreach ($items as $item): ?>
                    <li><a<?= attrs(['class' => 'site-header__link t-nav', 'href' => $item['href'], 'aria-current' => $isActive($item) ? 'page' : null]) ?>><?= e($item['label']) ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <span class="desktop-only"><?= component('lang-switch') ?></span>
            <?= component('theme-toggle') ?>
            <span class="desktop-only"><?= component('button', ['label' => t('common.cta'), 'variant' => 'inverse', 'href' => $contact]) ?></span>
            <?= component('icon-button', [
                'icon' => 'menu',
                'label' => t('common.menu.open'),
                'class' => 'mobile-only',
                'attributes' => ['data-menu-open' => true, 'aria-controls' => 'menu-mobile', 'aria-expanded' => 'false'],
            ]) ?>
        </div>
    </div>

    <div class="menu-panel" id="menu-mobile" role="dialog" aria-modal="true" aria-label="<?= e(lang() === 'fr' ? 'Menu' : 'Menu') ?>" hidden>
        <div class="menu-panel__top">
            <?= component('marque') ?>
            <?= component('icon-button', ['icon' => 'close', 'label' => t('common.menu.close'), 'attributes' => ['data-menu-close' => true]]) ?>
        </div>
        <nav aria-label="<?= e(lang() === 'fr' ? 'Navigation principale' : 'Main navigation') ?>">
            <ul class="menu-panel__links" role="list">
                <?php foreach ($items as $item): ?>
                    <li><a<?= attrs(['class' => 'menu-panel__link t-heading-2', 'href' => $item['href'], 'aria-current' => $isActive($item) ? 'page' : null, 'data-menu-close' => true]) ?>><?= e($item['label']) ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>
        <div class="menu-panel__bottom">
            <?= component('lang-switch') ?>
            <?= component('button', ['label' => t('common.cta'), 'variant' => 'inverse', 'href' => $contact, 'full' => true]) ?>
        </div>
    </div>
</header>
