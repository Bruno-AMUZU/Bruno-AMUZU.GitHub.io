<?php
/**
 * Field — champ de formulaire avec libellé et message d'erreur (components.md → Field).
 *
 * @var string      $name
 * @var string      $label
 * @var string      $type        'text' | 'email' | 'password' | 'url' | 'number' | 'date' | 'textarea'
 * @var string      $value
 * @var string|null $placeholder
 * @var string|null $error       message d'erreur déjà traduit
 * @var string|null $help        aide sous le champ
 * @var bool        $required
 * @var array       $attributes  attributs du champ (autocomplete, maxlength…)
 */
$type ??= 'text';
$id = $id ?? 'f-' . preg_replace('/[^a-z0-9_-]/i', '-', $name);
$describedBy = trim((!empty($error) ? "{$id}-error " : '') . (!empty($help) ? "{$id}-help" : ''));
$fieldAttributes = [
    'id' => $id,
    'name' => $name,
    'class' => classes('field__input', 't-body', $type === 'textarea' ? 'field__input--area' : null),
    'placeholder' => $placeholder ?? null,
    'required' => !empty($required),
    'aria-invalid' => !empty($error) ? 'true' : null,
    'aria-describedby' => $describedBy !== '' ? $describedBy : null,
    ...($attributes ?? []),
];
?>
<div class="<?= e(classes('field', !empty($error) ? 'field--error' : null, $class ?? null)) ?>">
    <label class="field__label t-small" for="<?= e($id) ?>"><?= e($label) ?></label>
    <?php if ($type === 'textarea'): ?>
        <textarea<?= attrs(['rows' => 4, ...$fieldAttributes]) ?>><?= e($value ?? '') ?></textarea>
    <?php else: ?>
        <input<?= attrs(['type' => $type, 'value' => $value ?? '', ...$fieldAttributes]) ?>>
    <?php endif ?>
    <?php if (!empty($help)): ?>
        <p class="field__help t-caption muted" id="<?= e($id) ?>-help"><?= e($help) ?></p>
    <?php endif ?>
    <?php if (!empty($error)): ?>
        <p class="field__error t-caption" id="<?= e($id) ?>-error"><?= e($error) ?></p>
    <?php endif ?>
</div>
