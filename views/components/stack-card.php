<?php
/**
 * StackCard — une catégorie de la stack et ses outils (components.md → StackCard).
 *
 * @var string                                                    $title
 * @var list<array{name: string, tech?: ?string, mono?: ?string}> $items
 * @var bool                                                      $inverted catégorie IA : fond inv
 */
$inverted = !empty($inverted);
?>
<article class="<?= e(classes('stack-card', $inverted ? 'stack-card--inv' : null)) ?>">
    <header class="stack-card__head">
        <h3 class="t-heading-2"><?= e($title) ?></h3>
        <span class="stack-card__count t-caption"><?= e(t('home.stack.count', ['n' => count($items)])) ?></span>
    </header>
    <ul class="stack-card__list" role="list">
        <?php foreach ($items as $item): ?>
            <?= component('tech-chip', [...$item, 'inverted' => $inverted]) ?>
        <?php endforeach ?>
    </ul>
</article>
