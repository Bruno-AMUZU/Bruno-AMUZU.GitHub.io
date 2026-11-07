<?php
/**
 * SectionHeader — titre de section heading-1, lien facultatif à droite (components.md → SectionHeader).
 *
 * @var string      $title
 * @var string|null $id        identifiant du titre (aria-labelledby de la section)
 * @var string|null $linkLabel « Tous les projets → »
 * @var string|null $linkHref
 */
?>
<div class="section-header">
    <h2<?= attrs(['class' => 't-heading-1', 'id' => $id ?? null]) ?>><?= e($title) ?></h2>
    <?php if (!empty($linkLabel) && !empty($linkHref)): ?>
        <a class="section-header__link t-label" href="<?= e($linkHref) ?>"><?= e($linkLabel) ?></a>
    <?php endif ?>
</div>
