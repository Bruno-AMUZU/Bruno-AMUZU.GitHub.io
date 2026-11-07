<?php
/**
 * ProjectCard — carte projet, entièrement cliquable (components.md → ProjectCard).
 * Toutes les cartes projet ont la même taille ; pas de variante « vedette ».
 *
 * @var string       $title
 * @var string       $href
 * @var string|null  $image   URL de la capture ; sans capture : bloc inv « [Visuel du projet] »
 * @var list<string> $tags
 * @var string|null  $headingLevel 'h2' ou 'h3' selon la page
 */
$tag = $headingLevel ?? 'h3';
?>
<article class="project-card">
    <div class="project-card__visual">
        <?php if (!empty($image)): ?>
            <img src="<?= e($image) ?>" alt="" loading="lazy" decoding="async">
        <?php else: ?>
            <span class="project-card__placeholder t-small"><?= e(lang() === 'fr' ? '[Visuel du projet]' : '[Project visual]') ?></span>
        <?php endif ?>
    </div>
    <div class="project-card__body">
        <div class="project-card__head">
            <<?= $tag ?> class="t-heading-2"><a class="project-card__link" href="<?= e($href) ?>"><?= e($title) ?></a></<?= $tag ?>>
            <span class="project-card__arrow" aria-hidden="true"><?= icon('arrow') ?></span>
        </div>
        <?php if (!empty($tags)): ?>
            <div class="tags">
                <?php foreach ($tags as $tagLabel): ?>
                    <?= component('tag', ['text' => $tagLabel]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</article>
