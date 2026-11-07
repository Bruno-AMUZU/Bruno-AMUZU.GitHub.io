<?php
/**
 * Pill — pastille d'information (components.md → Pill).
 *
 * @var string $text
 * @var string $variant 'info' (défaut, fond soft) | 'status' (point accent) | 'accent' (fond accent, tags du Lab)
 */
$variant ??= 'info';
?>
<span class="<?= e(classes('pill', 'pill--' . $variant, $variant === 'accent' ? 't-caption' : 't-small', $class ?? null)) ?>"><?php if ($variant === 'status'): ?><span class="pill__dot" aria-hidden="true"></span><?php endif ?><?= e($text) ?></span>
