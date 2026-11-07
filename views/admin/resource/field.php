<?php
/**
 * Un champ du formulaire d'édition, selon son type (App\Controller\Admin\Resource\Field).
 *
 * @var App\Controller\Admin\Resource\Field $field
 * @var mixed       $value
 * @var string|null $error
 * @var string      $inputName nom HTML (tr[fr][title] pour un champ traduit)
 * @var string      $inputId
 * @var array<int, App\Model\Entity\File> $files fichiers déjà envoyés, par id
 */
$common = ['help' => $field->help, 'placeholder' => $field->placeholder, 'required' => $field->required, 'error' => $error, 'id' => $inputId];
?>
<?php switch ($field->type):
    case 'textarea':
    case 'markdown': ?>
        <?= component('field', [...$common, 'name' => $inputName, 'label' => $field->label, 'type' => 'textarea', 'value' => (string) $value,
            'attributes' => ['rows' => $field->type === 'markdown' ? 16 : 4, 'maxlength' => $field->max, 'class' => $field->type === 'markdown' ? 'field__input t-code field__input--area' : null]]) ?>
        <?php break ?>

    <?php case 'select': ?>
        <div class="<?= e(classes('field', $error ? 'field--error' : null)) ?>">
            <label class="field__label t-small" for="<?= e($inputId) ?>"><?= e($field->label) ?></label>
            <select class="field__input t-body" id="<?= e($inputId) ?>" name="<?= e($inputName) ?>"<?= $field->required ? ' required' : '' ?>>
                <?php if (!$field->required): ?><option value="">—</option><?php endif ?>
                <?php foreach ($field->options as $optionValue => $optionLabel): ?>
                    <option value="<?= e($optionValue) ?>"<?= (string) $optionValue === (string) $value ? ' selected' : '' ?>><?= e($optionLabel) ?></option>
                <?php endforeach ?>
            </select>
            <?php if ($error): ?><p class="field__error t-caption"><?= e($error) ?></p><?php endif ?>
        </div>
        <?php break ?>

    <?php case 'toggle': ?>
        <label class="admin-toggle t-body">
            <input type="hidden" name="<?= e($inputName) ?>" value="0">
            <input type="checkbox" name="<?= e($inputName) ?>" value="1"<?= $value ? ' checked' : '' ?>>
            <span><?= e($field->label) ?></span>
        </label>
        <?php break ?>

    <?php case 'tags': ?>
        <div class="field admin-tags" data-tags="<?= e($inputName) ?>">
            <span class="field__label t-small"><?= e($field->label) ?></span>
            <ul class="tags admin-tags__list" role="list">
                <?php foreach ((array) $value as $tag): ?>
                    <li class="tag t-caption admin-tags__item">
                        <input type="hidden" name="<?= e($inputName) ?>[]" value="<?= e($tag) ?>">
                        <?= e($tag) ?>
                        <button type="button" class="admin-tags__remove" aria-label="Retirer <?= e($tag) ?>"><?= icon('close', null, 14) ?></button>
                    </li>
                <?php endforeach ?>
            </ul>
            <label class="sr-only" for="<?= e($inputId) ?>"><?= e(t('admin.edit.addTech')) ?></label>
            <input class="field__input t-body" type="text" id="<?= e($inputId) ?>" name="<?= e($inputName) ?>_new" placeholder="<?= e(t('admin.edit.addTech')) ?>">
            <p class="field__help t-caption muted"><?= e(t('admin.form.tagsHelp')) ?></p>
        </div>
        <?php break ?>

    <?php case 'image':
    case 'pdf':
        $file = $value ? ($files[(int) $value] ?? null) : null; ?>
        <div class="field">
            <span class="field__label t-small"><?= e($field->label) ?></span>
            <label class="dropzone" for="<?= e($inputId) ?>">
                <?php if ($file && $file->isImage()): ?>
                    <img class="dropzone__preview" src="<?= e($file->url()) ?>" alt="" data-preview>
                <?php elseif ($file): ?>
                    <span class="t-small"><?= e($file->originalName) ?></span>
                <?php else: ?>
                    <img class="dropzone__preview" alt="" data-preview hidden>
                <?php endif ?>
                <span class="t-small muted"><?= e($field->type === 'pdf' ? t('admin.form.dropPdf') : t('admin.edit.dropzone')) ?></span>
                <input class="sr-only" type="file" id="<?= e($inputId) ?>" name="<?= e($inputName) ?>"
                       accept="<?= $field->type === 'pdf' ? 'application/pdf' : 'image/jpeg,image/png,image/webp' ?>">
            </label>
            <?php if ($file): ?>
                <p class="t-caption muted"><a href="<?= e($file->url()) ?>" target="_blank" rel="noopener"><?= e($file->originalName) ?></a></p>
                <label class="admin-toggle t-small"><input type="checkbox" name="remove[<?= e($field->name) ?>]" value="1"> <?= e(t('admin.form.removeFile')) ?></label>
            <?php endif ?>
            <?php if ($field->help): ?><p class="field__help t-caption muted"><?= e($field->help) ?></p><?php endif ?>
        </div>
        <?php break ?>

    <?php case 'gallery': ?>
        <div class="field">
            <span class="field__label t-small"><?= e($field->label) ?></span>
            <?php if ((array) $value !== []): ?>
                <ul class="admin-gallery" role="list">
                    <?php foreach ((array) $value as $fileId): ?>
                        <?php $file = $files[(int) $fileId] ?? null; if (!$file) continue; ?>
                        <li class="admin-gallery__item">
                            <img src="<?= e($file->url()) ?>" alt="">
                            <label class="admin-toggle t-caption"><input type="checkbox" name="keep[<?= e($field->name) ?>][]" value="<?= e($file->id) ?>" checked> <?= e(t('admin.form.keep')) ?></label>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <label class="dropzone dropzone--small" for="<?= e($inputId) ?>">
                <span class="t-small muted"><?= e(t('admin.form.addImages')) ?></span>
                <input class="sr-only" type="file" id="<?= e($inputId) ?>" name="<?= e($inputName) ?>[]" accept="image/jpeg,image/png,image/webp" multiple>
            </label>
        </div>
        <?php break ?>

    <?php default:
        $type = match ($field->type) { 'url' => 'url', 'email' => 'email', 'number' => 'number', 'date' => 'date', 'month' => 'month', default => 'text' };
        $display = $field->type === 'month' && is_string($value) ? substr($value, 0, 7) : $value; ?>
        <?= component('field', [...$common, 'name' => $inputName, 'label' => $field->label, 'type' => $type, 'value' => is_scalar($display) ? (string) $display : '',
            'attributes' => ['maxlength' => $field->max]]) ?>
<?php endswitch ?>
