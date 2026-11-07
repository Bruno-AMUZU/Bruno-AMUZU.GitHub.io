<?php
/**
 * PageHeader — en-tête des pages internes : titre display-l + introduction body-l muted, sans carte
 * (components.md → PageHeader).
 *
 * @var string      $title
 * @var string|null $intro  phrase d'introduction (masquée si vide)
 * @var bool        $narrow aligné sur la colonne de lecture de 720 px (mentions légales)
 */
?>
<header class="<?= e(classes('page-header', !empty($narrow) ? 'reading' : null)) ?>">
    <h1 class="t-display-l"><?= e($title) ?></h1>
    <?php if (!empty($intro)): ?>
        <p class="t-body-l muted"><?= e($intro) ?></p>
    <?php endif ?>
</header>
