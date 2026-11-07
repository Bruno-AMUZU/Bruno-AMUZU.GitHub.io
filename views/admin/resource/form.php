<?php
/**
 * Édition d'un contenu (design/pages/admin-edition.md) : colonne principale avec les onglets FR / EN
 * et les champs traduits ; colonne latérale avec les panneaux Publication, Informations, Médias…
 *
 * @var string        $title
 * @var array<string, string> $labels
 * @var int|null      $id
 * @var array<string, mixed>  $values
 * @var array<string, string> $errors
 * @var list<App\Controller\Admin\Resource\Field> $fields
 * @var bool          $hasStatus
 * @var string        $action
 * @var string|null   $deleteAction
 * @var string|null   $previewUrl
 * @var list<string>  $incomplete langues incomplètes
 */
use App\Model\Repository\FileRepository;

$translated = array_values(array_filter($fields, fn ($f) => $f->translated));
$panels = [];
foreach ($fields as $field) {
    if (!$field->translated) {
        $panels[$field->panel ?? 'Informations'][] = $field;
    }
}

// Fichiers déjà envoyés, pour les aperçus.
$fileIds = [];
foreach ($fields as $field) {
    if ($field->isFile()) {
        foreach (array_filter((array) ($values[$field->name] ?? [])) as $fileId) {
            $fileIds[] = (int) $fileId;
        }
    }
}
$fileRepository = App\Core\App::get(FileRepository::class);
$files = [];
foreach ($fileIds as $fileId) {
    if ($file = $fileRepository->find($fileId)) {
        $files[$fileId] = $file;
    }
}

$field = fn ($f, string $name, string $id, mixed $value, ?string $error) => App\Core\View::partial('admin/resource/field', [
    'field' => $f, 'inputName' => $name, 'inputId' => $id, 'value' => $value, 'error' => $error, 'files' => $files,
]);

$actions = ($previewUrl ? component('button', ['label' => t('admin.edit.preview'), 'variant' => 'secondary', 'href' => $previewUrl, 'attributes' => ['target' => '_blank', 'rel' => 'noopener']]) : '')
    . component('button', ['label' => t('admin.edit.save'), 'variant' => 'primary', 'type' => 'submit', 'attributes' => ['form' => 'edit-form']]);
?>
<?= component('admin-topbar', ['title' => $title, 'backLabel' => $labels['back'], 'backHref' => $base, 'actions' => $actions]) ?>

<?php if ($errors !== []): ?>
    <div class="admin-alert t-small" role="alert">
        <?= e(t('admin.form.hasErrors')) ?>
        <?php if (isset($errors['_files'])): ?> <?= e($errors['_files']) ?><?php endif ?>
    </div>
<?php endif ?>

<form class="admin-edit" id="edit-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="admin-edit__main">
        <section class="admin-card">
            <div class="lang-tabs" role="tablist" aria-label="Langue du contenu">
                <?php foreach (['fr', 'en'] as $i => $lang): ?>
                    <button type="button" class="lang-tabs__tab t-label" role="tab" id="tab-<?= $lang ?>" aria-controls="panel-<?= $lang ?>"
                            aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" data-tab="<?= $lang ?>">
                        <?= strtoupper($lang) ?>
                        <?php if (in_array($lang, $incomplete, true)): ?>
                            <span class="lang-tabs__dot" title="<?= e(t('admin.edit.incomplete')) ?>"></span><span class="sr-only"> — <?= e(t('admin.edit.incomplete')) ?></span>
                        <?php endif ?>
                    </button>
                <?php endforeach ?>
            </div>
            <?php foreach (['fr', 'en'] as $i => $lang): ?>
                <div class="lang-tabs__panel stack" role="tabpanel" id="panel-<?= $lang ?>" aria-labelledby="tab-<?= $lang ?>" lang="<?= $lang ?>" data-panel="<?= $lang ?>">
                    <?php foreach ($translated as $f): ?>
                        <?= $field($f, "tr[{$lang}][{$f->name}]", "f-{$lang}-{$f->name}", $values['translations'][$lang][$f->name] ?? '', $errors["tr.{$lang}.{$f->name}"] ?? null) ?>
                    <?php endforeach ?>
                </div>
            <?php endforeach ?>
        </section>
    </div>

    <div class="admin-edit__side">
        <?php if ($hasStatus): ?>
            <section class="admin-card stack">
                <h2 class="t-heading-3"><?= e(t('admin.edit.panels.0')) ?></h2>
                <div><?= component('status-badge', ['status' => $values['status'] ?? 'draft']) ?></div>
                <?= component('choice-chips', [
                    'name' => 'status',
                    'legend' => t('admin.edit.fields.status'),
                    'options' => ['published' => t('admin.list.status.published'), 'draft' => t('admin.list.status.draft')],
                    'selected' => $values['status'] ?? 'draft',
                ]) ?>
                <?php if (!empty($values['updated_at'])): ?>
                    <p class="t-small muted"><?= e(t('admin.edit.fields.updated')) ?> <?= e(format_date((string) $values['updated_at'], 'fr')) ?></p>
                <?php endif ?>
            </section>
        <?php endif ?>

        <?php foreach ($panels as $panel => $panelFields): ?>
            <section class="admin-card stack">
                <h2 class="t-heading-3"><?= e($panel) ?></h2>
                <?php foreach ($panelFields as $f): ?>
                    <?= $field($f, $f->name, 'f-' . $f->name, $values[$f->name] ?? ($f->type === 'tags' || $f->type === 'gallery' ? [] : ''), $errors[$f->name] ?? null) ?>
                <?php endforeach ?>
            </section>
        <?php endforeach ?>
    </div>
</form>

<?php if ($deleteAction): ?>
    <form class="admin-delete" method="post" action="<?= e($deleteAction) ?>" data-confirm="<?= e(t('admin.form.confirmDelete', ['title' => $title])) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="admin-delete__button t-label"><?= e($labels['delete']) ?></button>
    </form>
<?php endif ?>
