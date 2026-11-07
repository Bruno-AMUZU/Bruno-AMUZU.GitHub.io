<?php
/**
 * StatCard — chiffre display-l + libellé small (components.md → Back-office).
 *
 * @var int|string  $value
 * @var string      $label
 * @var string|null $href
 */
$tag = !empty($href) ? 'a' : 'div';
?>
<<?= $tag ?> class="stat-card"<?= !empty($href) ? ' href="' . e($href) . '"' : '' ?>>
    <span class="t-display-l"><?= e($value) ?></span>
    <span class="t-small muted"><?= e($label) ?></span>
</<?= $tag ?>>
