<?php
/**
 * Button — action principale ou secondaire (components.md → Button).
 *
 * @var string      $label
 * @var string      $variant  'primary' (accent) | 'secondary' (soft) | 'inverse' (inv)
 * @var string|null $href     lien : rendu en <a> ; sinon <button>
 * @var string      $type     'button' | 'submit'
 * @var string|null $icon     icône après le libellé
 * @var bool        $full     pleine largeur
 * @var bool        $disabled
 * @var array       $attributes attributs HTML supplémentaires
 */
$class = classes('btn', 'btn--' . ($variant ?? 'primary'), !empty($full) ? 'btn--full' : null, 't-label', $class ?? null);
$inner = e($label) . (!empty($icon) ? icon($icon) : '');
?>
<?php if (!empty($href)): ?>
<a<?= attrs(['class' => $class, 'href' => $href, 'aria-disabled' => !empty($disabled) ? 'true' : null, ...($attributes ?? [])]) ?>><?= $inner ?></a>
<?php else: ?>
<button<?= attrs(['class' => $class, 'type' => $type ?? 'button', 'disabled' => !empty($disabled), ...($attributes ?? [])]) ?>><?= $inner ?></button>
<?php endif ?>
