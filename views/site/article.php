<?php
/**
 * Article du Lab (design/pages/article.md) : retour, en-tête, vidéo facultative, corps, articles liés.
 *
 * @var App\Model\Entity\Article       $article
 * @var string                         $html    contenu Markdown converti (HTML de l'auteur échappé)
 * @var list<App\Model\Entity\Article> $related
 * @var bool                           $preview
 */
$categories = array_slice(t_list('labPage.filters'), 1);
$index = array_search($article->category, App\Model\Entity\Article::CATEGORIES, true);
?>
<article class="container stack article-page">
    <?php if ($preview): ?>
        <p class="preview-banner reading t-small" role="status"><?= e(lang() === 'fr' ? 'Aperçu : cet article est un brouillon, invisible pour les visiteurs.' : 'Preview: this article is a draft, hidden from visitors.') ?></p>
    <?php endif ?>

    <div class="reading article-page__column">
        <a class="project-page__back t-label" href="<?= e(url('lab')) ?>"><?= e(t('articlePage.back')) ?></a>

        <header class="article-page__head">
            <?= component('pill', ['text' => $categories[$index === false ? 0 : $index] ?? $article->category, 'variant' => 'accent']) ?>
            <h1 class="t-display-l"><?= e($article->title) ?></h1>
            <?php if (article_meta($article) !== ''): ?>
                <p class="t-small muted"><?= e(article_meta($article)) ?></p>
            <?php endif ?>
        </header>

        <?php if ($article->youtubeId() !== null): ?>
            <?= component('video-player', ['id' => $article->youtubeId(), 'title' => $article->title]) ?>
        <?php endif ?>

        <div class="prose t-body-l">
            <?= $html ?>
        </div>
    </div>

    <?php if ($related !== []): ?>
        <section class="article-page__related" aria-labelledby="related-title">
            <h2 class="t-heading-2" id="related-title"><?= e(t('articlePage.related')) ?></h2>
            <ul class="grid grid--3" role="list">
                <?php foreach ($related as $item): ?>
                    <li><?= component('lab-card', ['article' => $item]) ?></li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>
</article>
