<?php
/**
 * Lab — liste des articles (design/pages/lab.md).
 *
 * @var list<App\Model\Entity\Article> $articles
 * @var list<array{label: string, href: string, active: bool}> $filters
 */
?>
<div class="container stack page-stack">
    <?= component('page-header', ['title' => t('labPage.title'), 'intro' => t('labPage.intro')]) ?>

    <?= component('choice-chips', ['label' => lang() === 'fr' ? 'Filtrer les articles' : 'Filter articles', 'links' => $filters]) ?>

    <?php if ($articles === []): ?>
        <p class="t-body-l muted"><?= e(t('labPage.empty')) ?></p>
    <?php else: ?>
        <ul class="grid grid--3" role="list">
            <?php foreach ($articles as $article): ?>
                <li><?= component('lab-card', ['article' => $article, 'headingLevel' => 'h2']) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
