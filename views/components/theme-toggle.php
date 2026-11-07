<?php
/**
 * Bascule de thème : IconButton lune / soleil (l'icône montre l'action). Piloté par assets/js/theme.js.
 *
 * @var string $variant 'soft' (défaut) | 'inv-soft'
 */
?>
<button<?= attrs([
    'type' => 'button',
    'class' => classes('icon-btn', 'icon-btn--' . ($variant ?? 'soft'), 'theme-toggle'),
    'aria-label' => t('common.theme.toDark'),
    'data-theme-toggle' => true,
    'data-label-to-dark' => t('common.theme.toDark'),
    'data-label-to-light' => t('common.theme.toLight'),
    'hidden' => true,
]) ?>><?= str_replace('class="icon"', 'class="icon icon-moon"', icon('moon')) ?><?= str_replace('class="icon"', 'class="icon icon-sun"', icon('sun')) ?></button>
