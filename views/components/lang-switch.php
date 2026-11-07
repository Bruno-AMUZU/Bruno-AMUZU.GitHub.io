<?php
/**
 * LangSwitch — « FR · EN », langue active en ink, l'autre en muted (components.md → LangSwitch).
 * Chaque lien mène à la même page dans l'autre langue.
 */
$current = lang();
?>
<nav class="lang-switch t-small" aria-label="<?= e($current === 'fr' ? 'Langue' : 'Language') ?>">
    <?php foreach (['fr', 'en'] as $i => $code): ?>
        <?php if ($i > 0): ?><span class="lang-switch__sep" aria-hidden="true">·</span><?php endif ?>
        <a<?= attrs([
            'class' => classes('lang-switch__link', $code === $current ? 'is-active' : null),
            'href' => switch_lang_url($code),
            'hreflang' => $code,
            'lang' => $code,
            'aria-current' => $code === $current ? 'true' : null,
        ]) ?>><?= e(t('common.lang.' . $code)) ?></a>
    <?php endforeach ?>
</nav>
