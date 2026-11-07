<?php
/**
 * Liste d'un type de contenu (design/pages/admin-liste.md).
 *
 * @var array<string, string> $labels
 * @var list<array<string, mixed>> $rows
 * @var string      $search
 * @var string|null $status
 * @var bool        $hasStatus
 * @var bool        $sortable
 * @var string      $base
 */
$filters = t_list('admin.list.filters');
$query = fn (?string $statut) => $base . '?' . http_build_query(array_filter(['q' => $search, 'statut' => $statut]));
$canReorder = $sortable && $search === '' && $status === null; // réordonner n'a de sens que sur la liste complète
?>
<?= component('admin-topbar', [
    'title' => $labels['title'],
    'actions' => component('button', ['label' => t('admin.list.new'), 'variant' => 'primary', 'href' => $base . '/nouveau']),
]) ?>

<div class="admin-toolbar">
    <form class="admin-toolbar__search" method="get" action="<?= e($base) ?>" role="search">
        <?php if ($status !== null): ?><input type="hidden" name="statut" value="<?= e($status === 'published' ? 'publie' : 'brouillon') ?>"><?php endif ?>
        <label class="sr-only" for="q"><?= e(t('admin.list.search')) ?></label>
        <input class="field__input t-body" type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="<?= e(t('admin.list.search')) ?>">
    </form>
    <?php if ($hasStatus): ?>
        <?= component('choice-chips', ['label' => 'Statut', 'links' => [
            ['label' => $filters[0] ?? 'Tous', 'href' => $query(null), 'active' => $status === null],
            ['label' => $filters[1] ?? 'Publiés', 'href' => $query('publie'), 'active' => $status === 'published'],
            ['label' => $filters[2] ?? 'Brouillons', 'href' => $query('brouillon'), 'active' => $status === 'draft'],
        ]]) ?>
    <?php endif ?>
</div>

<?php if ($rows === []): ?>
    <div class="admin-empty">
        <p class="t-body-l muted"><?= e($search !== '' || $status !== null ? t('admin.list.noResult') : $labels['empty']) ?></p>
        <?= component('button', ['label' => t('admin.list.new'), 'variant' => 'primary', 'href' => $base . '/nouveau']) ?>
    </div>
<?php else: ?>
    <ul class="data-table" role="list"<?= $canReorder ? ' data-reorder="' . e($base . '/ordre') . '"' : '' ?>>
        <?php foreach ($rows as $row): ?>
            <?= component('data-table-row', [
                'row' => $row,
                'editUrl' => $base . '/' . $row['id'],
                'deleteUrl' => $base . '/' . $row['id'] . '/supprimer',
                'deleteConfirm' => t('admin.form.confirmDelete', ['title' => $row['title']]),
                'sortable' => $canReorder,
            ]) ?>
        <?php endforeach ?>
    </ul>
    <?php if ($canReorder): ?>
        <p class="t-caption muted desktop-only"><?= e(t('admin.list.reorderHelp')) ?></p>
    <?php endif ?>
<?php endif ?>
