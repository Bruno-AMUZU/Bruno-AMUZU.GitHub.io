<?php
/**
 * AdminSidebar — navigation du back-office (components.md → Back-office).
 * Desktop : colonne 240 px fond inv. Mobile : barre haute inv + bouton menu qui déplie les mêmes entrées.
 * Une entrée n'apparaît que si sa page existe ; l'entrée active est sur fond accent.
 *
 * @var int $unread nombre de messages non lus (compteur de l'entrée Messages)
 */
$labels = t_list('admin.nav');
$entries = [
    ['admin.dashboard', $labels[0] ?? 'Tableau de bord'],
    ['admin.projects', $labels[1] ?? 'Projets'],
    ['admin.certifications', $labels[2] ?? 'Certifications'],
    ['admin.timeline', $labels[3] ?? 'Parcours'],
    ['admin.skills', $labels[4] ?? 'Compétences'],
    ['admin.services', $labels[5] ?? 'Services'],
    ['admin.lab', $labels[6] ?? 'Lab'],
    ['admin.messages', $labels[7] ?? 'Messages'],
    ['admin.settings', $labels[8] ?? 'Réglages'],
];
$current = (string) App\Core\App::get(App\Core\UrlGenerator::class)->currentName();
$isActive = fn (string $name) => $current === $name || ($name !== 'admin.dashboard' && str_starts_with($current, $name . '.'));
$unread = (int) ($unread ?? 0);
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar__top">
        <?= component('marque', ['href' => url('admin.dashboard'), 'inverse' => true]) ?>
        <?= component('icon-button', [
            'icon' => 'menu', 'label' => t('common.menu.open'), 'variant' => 'inv-soft', 'class' => 'mobile-only',
            'attributes' => ['data-admin-menu' => true, 'aria-controls' => 'admin-nav', 'aria-expanded' => 'false'],
        ]) ?>
    </div>

    <div class="admin-sidebar__panel" id="admin-nav">
        <nav aria-label="Back-office">
            <ul class="admin-sidebar__list" role="list">
                <?php foreach ($entries as [$name, $label]): ?>
                    <?php if (!has_route($name)) continue; ?>
                    <li>
                        <a<?= attrs([
                            'class' => classes('admin-sidebar__link t-label', $isActive($name) ? 'is-active' : null),
                            'href' => url($name),
                            'aria-current' => $isActive($name) ? 'page' : null,
                        ]) ?>>
                            <span><?= e($label) ?></span>
                            <?php if ($name === 'admin.messages' && $unread > 0): ?>
                                <span class="admin-sidebar__count t-caption"><?= e($unread) ?><span class="sr-only"> non lus</span></span>
                            <?php endif ?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>

        <div class="admin-sidebar__bottom">
            <a class="admin-sidebar__link t-label" href="<?= e(url('home', [], 'fr')) ?>" target="_blank" rel="noopener"><?= e(t('admin.viewSite')) ?></a>
            <form method="post" action="<?= e(url('admin.logout')) ?>">
                <?= csrf_field() ?>
                <button class="admin-sidebar__link t-label" type="submit"><?= e(t('admin.logout')) ?></button>
            </form>
        </div>
    </div>
</aside>
