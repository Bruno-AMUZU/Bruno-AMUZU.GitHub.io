<?php
/**
 * DataTableRow — ligne de la liste d'un type de contenu (components.md → DataTable).
 * Colonnes : poignée, miniature 64×40, titre, langues, statut (96 px), date, actions.
 * En mobile : carte empilée, sans poignée ni date.
 *
 * @var array{id: int, title: string, thumb: ?string, langs: list<string>, status: ?string, updated_at: ?string} $row
 * @var string $editUrl
 * @var string $deleteUrl
 * @var string $deleteConfirm
 * @var bool   $sortable
 */
?>
<li class="data-row" data-id="<?= e($row['id']) ?>"<?= $sortable ? ' draggable="true"' : '' ?>>
    <?php if ($sortable): ?>
        <span class="data-row__grip desktop-only" title="Glisser pour réordonner" aria-hidden="true"><?= icon('grip') ?></span>
    <?php endif ?>
    <span class="data-row__thumb">
        <?php if ($row['thumb']): ?><img src="<?= e($row['thumb']) ?>" alt="" width="64" height="40" loading="lazy"><?php endif ?>
    </span>
    <a class="data-row__title t-label" href="<?= e($editUrl) ?>"><?= e($row['title']) ?></a>
    <span class="data-row__langs">
        <?php foreach ($row['langs'] as $lang): ?><?= component('tag', ['text' => strtoupper($lang)]) ?><?php endforeach ?>
    </span>
    <span class="data-row__status"><?php if ($row['status']): ?><?= component('status-badge', ['status' => $row['status']]) ?><?php endif ?></span>
    <span class="data-row__date t-small muted desktop-only"><?= e(format_date($row['updated_at'], 'fr')) ?></span>
    <span class="data-row__actions">
        <?= component('icon-button', ['icon' => 'edit', 'label' => 'Modifier « ' . $row['title'] . ' »', 'href' => $editUrl]) ?>
        <form method="post" action="<?= e($deleteUrl) ?>" data-confirm="<?= e($deleteConfirm) ?>">
            <?= csrf_field() ?>
            <?= component('icon-button', ['icon' => 'trash', 'label' => 'Supprimer « ' . $row['title'] . ' »', 'type' => 'submit']) ?>
        </form>
    </span>
</li>
