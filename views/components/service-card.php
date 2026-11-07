<?php
/**
 * ServiceCard — service avec une mini-illustration (components.md → ServiceCard).
 *
 * @var string $illustration 'web' | 'apps' | 'ia'
 * @var string $title
 * @var string $text
 * @var bool   $inverted     carte IA : fond inv
 */
$diagram = t_list('home.services.diagram');
?>
<article class="<?= e(classes('service-card', !empty($inverted) ? 'service-card--inv' : null)) ?>">
    <div class="service-card__art service-art service-art--<?= e($illustration) ?>" aria-hidden="true">
        <?php if ($illustration === 'web'): ?>
            <div class="service-art__window">
                <div class="service-art__dots"><span></span><span></span><span></span></div>
                <div class="service-art__title"></div>
                <div class="service-art__line"></div>
                <div class="service-art__line service-art__line--short"></div>
                <div class="service-art__button"></div>
            </div>
        <?php elseif ($illustration === 'apps'): ?>
            <div class="service-art__bars">
                <span class="is-line"></span><span class="is-ink"></span><span class="is-line"></span><span class="is-accent"></span><span class="is-line"></span>
            </div>
        <?php else: ?>
            <div class="service-art__flow">
                <span class="service-art__node t-caption"><?= e($diagram[0] ?? '') ?></span>
                <span class="service-art__link"></span>
                <span class="service-art__agent t-caption"><?= e($diagram[1] ?? '') ?></span>
                <span class="service-art__link"></span>
                <span class="service-art__node t-caption"><?= e($diagram[2] ?? '') ?></span>
            </div>
        <?php endif ?>
    </div>
    <div class="service-card__body">
        <h3 class="t-heading-2"><?= e($title) ?></h3>
        <p class="service-card__text t-body"><?= e($text) ?></p>
    </div>
</article>
