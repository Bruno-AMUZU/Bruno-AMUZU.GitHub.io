<?php
/**
 * Mentions légales : titre aligné sur la colonne de 720 px, puis une carte avec les blocs.
 *
 * @var list<array{title: string, text: string}> $blocks
 */
?>
<div class="container stack page-stack">
    <?= component('page-header', ['title' => t('legal.title'), 'narrow' => true]) ?>
    <div class="reading legal">
        <?php foreach ($blocks as $block): ?>
            <section class="legal__block">
                <h2 class="t-heading-2"><?= e($block['title'] ?? '') ?></h2>
                <p class="t-body muted"><?= nl2br(e($block['text'] ?? '')) ?></p>
            </section>
        <?php endforeach ?>
    </div>
</div>
