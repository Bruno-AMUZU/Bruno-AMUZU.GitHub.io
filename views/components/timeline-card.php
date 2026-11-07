<?php
/**
 * TimelineCard — entrée de la frise Parcours (components.md → TimelineCard).
 *
 * @var string      $kind    « Formation » / « Expérience »
 * @var string      $period
 * @var string      $title
 * @var string|null $org
 * @var string|null $place
 * @var string|null $text
 */
$where = implode(' · ', array_filter([$org ?? null, $place ?? null]));
?>
<li class="timeline-card">
    <span class="timeline-card__marker" aria-hidden="true"></span>
    <article class="timeline-card__card">
        <?= component('pill', ['text' => $kind]) ?>
        <p class="t-small muted"><?= e($period) ?></p>
        <h3 class="t-heading-2"><?= e($title) ?></h3>
        <?php if ($where !== ''): ?><p class="t-body"><?= e($where) ?></p><?php endif ?>
        <?php if (!empty($text)): ?><p class="t-body muted"><?= e($text) ?></p><?php endif ?>
    </article>
</li>
