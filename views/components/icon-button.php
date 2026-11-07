<?php
/**
 * IconButton — bouton rond 44 px avec une icône (components.md → IconButton).
 *
 * @var string      $icon
 * @var string      $label     libellé accessible (obligatoire : le bouton n'a pas de texte)
 * @var string      $variant   'soft' (défaut) | 'inv' | 'inv-soft' | 'accent'
 * @var string|null $href
 * @var string      $type
 * @var array       $attributes
 */
$class = classes('icon-btn', 'icon-btn--' . ($variant ?? 'soft'), $class ?? null);
?>
<?php if (!empty($href)): ?>
<a<?= attrs(['class' => $class, 'href' => $href, 'aria-label' => $label, ...($attributes ?? [])]) ?>><?= icon($icon) ?></a>
<?php else: ?>
<button<?= attrs(['class' => $class, 'type' => $type ?? 'button', 'aria-label' => $label, ...($attributes ?? [])]) ?>><?= icon($icon) ?></button>
<?php endif ?>
