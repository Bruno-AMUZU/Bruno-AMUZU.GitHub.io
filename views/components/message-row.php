<?php
/**
 * MessageRow — ligne de message (components.md → Back-office).
 * Nom (label), Tag type de projet, extrait (small muted, 1 ligne, masquable), date (caption muted), StatusBadge.
 *
 * @var App\Model\Entity\Message $message
 * @var string $href
 * @var bool   $selected
 * @var bool   $excerpt afficher l'extrait (masqué sur le tableau de bord)
 */
$showExcerpt = $excerpt ?? true;
?>
<li>
    <a<?= attrs([
        'class' => classes('message-row', !empty($selected) ? 'is-selected' : null, $message->isUnread() ? 'is-unread' : null),
        'href' => $href,
        'aria-current' => !empty($selected) ? 'true' : null,
    ]) ?>>
        <span class="message-row__head">
            <span class="message-row__name t-label"><?= e($message->name) ?></span>
            <?= component('tag', ['text' => $message->typeLabel()]) ?>
        </span>
        <?php if ($showExcerpt): ?>
            <span class="message-row__excerpt t-small muted"><?= e($message->excerpt()) ?></span>
        <?php endif ?>
        <span class="message-row__meta">
            <span class="t-caption muted"><?= e(format_date($message->createdAt, 'fr')) ?></span>
            <?php if ($message->isUnread() || $message->status === 'archived'): ?>
                <?= component('status-badge', ['status' => $message->status]) ?>
            <?php endif ?>
        </span>
    </a>
</li>
