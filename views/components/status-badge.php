<?php
/**
 * StatusBadge — Publié (point success), Brouillon (bordure pointillée line), Non lu (point accent).
 *
 * @var string $status 'published' | 'draft' | 'unread' | 'read' | 'archived'
 */
$labels = [
    'published' => t('admin.list.status.published'),
    'draft' => t('admin.list.status.draft'),
    'unread' => t('admin.list.status.unread'),
    'read' => t('admin.list.status.read'),
    'archived' => t('admin.list.status.archived'),
];
?>
<span class="status-badge status-badge--<?= e($status) ?> t-caption"><?= e($labels[$status] ?? $status) ?></span>
