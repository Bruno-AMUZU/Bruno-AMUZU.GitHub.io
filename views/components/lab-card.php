<?php
/**
 * LabCard — article du Lab (components.md → LabCard), entièrement cliquable.
 * Dans la section inversée de l'accueil : fond invSoft. Sur la page Lab : version claire, fond surface,
 * avec date et temps de lecture.
 *
 * @var App\Model\Entity\Article $article
 * @var bool                     $inverted section inversée (accueil)
 * @var string                   $headingLevel
 */
$tag = $headingLevel ?? 'h3';
$categories = array_slice(t_list('labPage.filters'), 1);
$index = array_search($article->category, App\Model\Entity\Article::CATEGORIES, true);
?>
<article class="<?= e(classes('lab-card', !empty($inverted) ? 'lab-card--inv' : null)) ?>">
    <?= component('pixel-burst') ?>
    <?= component('pill', ['text' => $categories[$index === false ? 0 : $index] ?? $article->category, 'variant' => 'accent']) ?>
    <<?= $tag ?> class="t-heading-3"><a class="lab-card__link" href="<?= e(url('article', ['slug' => $article->slug])) ?>"><?= e($article->title) ?></a></<?= $tag ?>>
    <?php if (empty($inverted)): ?>
        <p class="t-caption muted"><?= e(article_meta($article)) ?></p>
    <?php endif ?>
</article>
