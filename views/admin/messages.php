<?php
/**
 * Messages (design/pages/admin-messages.md).
 *
 * @var list<App\Model\Entity\Message> $messages
 * @var App\Model\Entity\Message|null  $selected
 * @var string                $filter  filtre de l'URL (non-lus, tous, archives)
 * @var array<string, string> $filters
 */
$labels = t_list('admin.messages.filters'); // Non lus, Tous, Archivés
$links = [];
foreach (array_keys($filters) as $i => $slug) {
    $links[] = ['label' => $labels[$i] ?? $slug, 'href' => '/admin/messages?filtre=' . $slug, 'active' => $filter === $slug];
}
$query = $filter !== 'tous' ? '?filtre=' . $filter : '';
?>
<?= component('admin-topbar', ['title' => t('admin.messages.title')]) ?>

<div class="<?= e(classes('messages', $selected ? 'messages--has-selection' : null)) ?>">
    <section class="messages__list" aria-label="<?= e(t('admin.messages.title')) ?>">
        <?= component('choice-chips', ['label' => 'Filtre', 'links' => $links]) ?>
        <?php if ($messages === []): ?>
            <p class="admin-empty t-body muted"><?= e(t('admin.messages.empty')) ?></p>
        <?php else: ?>
            <ul class="messages__rows" role="list">
                <?php foreach ($messages as $message): ?>
                    <?= component('message-row', [
                        'message' => $message,
                        'href' => '/admin/messages/' . $message->id . $query,
                        'selected' => $selected?->id === $message->id,
                    ]) ?>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </section>

    <section class="messages__detail admin-card" aria-live="polite">
        <?php if ($selected === null): ?>
            <p class="t-body muted"><?= e(t('admin.messages.select')) ?></p>
        <?php else: ?>
            <a class="messages__back t-label muted mobile-only" href="/admin/messages<?= e($query) ?>">← <?= e(t('admin.messages.title')) ?></a>
            <header class="messages__head">
                <h2 class="t-heading-2"><?= e($selected->name) ?></h2>
                <p class="t-body"><a class="messages__email" href="mailto:<?= e($selected->email) ?>"><?= e($selected->email) ?></a></p>
                <p class="messages__meta">
                    <?= component('tag', ['text' => $selected->typeLabel()]) ?>
                    <span class="t-small muted"><?= e(format_date($selected->createdAt, 'fr')) ?> · <?= e(date('H:i', strtotime($selected->createdAt))) ?></span>
                </p>
            </header>
            <p class="messages__body t-body"><?= nl2br(e($selected->body)) ?></p>
            <div class="messages__actions">
                <?= component('button', [
                    'label' => t('admin.messages.reply'),
                    'variant' => 'primary',
                    'href' => 'mailto:' . $selected->email . '?subject=' . rawurlencode('Re: ' . t('admin.messages.subject')),
                ]) ?>
                <form method="post" action="/admin/messages/<?= e($selected->id) ?>/archiver">
                    <?= csrf_field() ?>
                    <?= component('button', ['label' => $selected->status === 'archived' ? t('admin.messages.unarchive') : t('admin.messages.archive'), 'variant' => 'secondary', 'type' => 'submit']) ?>
                </form>
                <form method="post" action="/admin/messages/<?= e($selected->id) ?>/non-lu">
                    <?= csrf_field() ?>
                    <?= component('button', ['label' => t('admin.messages.markUnread'), 'variant' => 'secondary', 'type' => 'submit']) ?>
                </form>
                <form method="post" action="/admin/messages/<?= e($selected->id) ?>/supprimer" data-confirm="<?= e(t('admin.messages.confirmDelete')) ?>">
                    <?= csrf_field() ?>
                    <?= component('icon-button', ['icon' => 'trash', 'label' => t('admin.messages.delete'), 'type' => 'submit']) ?>
                </form>
            </div>
        <?php endif ?>
    </section>
</div>
