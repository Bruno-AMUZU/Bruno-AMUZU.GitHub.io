<?php
/**
 * Tableau de bord : chiffres, derniers messages, actions rapides.
 *
 * @var array{unread: int, projects: int, articles: int, drafts: int} $stats
 * @var list<App\Model\Entity\Message> $latest
 */
$labels = t_list('admin.dashboard.stats'); // Messages non lus, Projets publiés, Articles publiés, Brouillons
$quick = t_list('admin.dashboard.quick');  // Nouveau projet, Nouvel article, Nouvelle certification
$actions = array_filter([
    has_route('admin.projects.create') ? [$quick[0], url('admin.projects.create')] : null,
    has_route('admin.lab.create') ? [$quick[1], url('admin.lab.create')] : null,
    has_route('admin.certifications.create') ? [$quick[2], url('admin.certifications.create')] : null,
]);
?>
<?= component('admin-topbar', ['title' => t('admin.dashboard.title')]) ?>

<section class="grid grid--4 grid--keep-2" aria-label="Chiffres">
    <?= component('stat-card', ['value' => $stats['unread'], 'label' => $labels[0], 'href' => url('admin.messages') . '?filtre=non-lus']) ?>
    <?= component('stat-card', ['value' => $stats['projects'], 'label' => $labels[1], 'href' => url('admin.projects') . '?statut=publie']) ?>
    <?= component('stat-card', ['value' => $stats['articles'], 'label' => $labels[2], 'href' => has_route('admin.lab') ? url('admin.lab') . '?statut=publie' : null]) ?>
    <?= component('stat-card', ['value' => $stats['drafts'], 'label' => $labels[3], 'href' => url('admin.projects') . '?statut=brouillon']) ?>
</section>

<section class="admin-card stack" aria-labelledby="latest-title">
    <h2 class="t-heading-2" id="latest-title"><?= e(t('admin.dashboard.latest')) ?></h2>
    <?php if ($latest === []): ?>
        <p class="t-body muted"><?= e(t('admin.messages.empty')) ?></p>
    <?php else: ?>
        <ul class="messages__rows" role="list">
            <?php foreach ($latest as $message): ?>
                <?= component('message-row', ['message' => $message, 'href' => '/admin/messages/' . $message->id, 'excerpt' => false]) ?>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <a class="t-label section-header__link" href="<?= e(url('admin.messages')) ?>"><?= e(t('admin.dashboard.allMessages')) ?></a>
</section>

<section class="stack" aria-label="Actions rapides">
    <div class="admin-quick">
        <?php foreach ($actions as [$label, $href]): ?>
            <?= component('button', ['label' => $label, 'variant' => 'secondary', 'href' => $href]) ?>
        <?php endforeach ?>
    </div>
</section>
