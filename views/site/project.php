<?php
/**
 * Projet — détail / étude de cas (design/pages/projet.md).
 *
 * @var App\Model\Entity\Project      $project
 * @var App\Model\Entity\Project|null $next
 * @var bool                          $preview brouillon affiché à un admin
 */
$cases = array_filter([
    'context' => $project->context,
    'solution' => $project->solution,
    'result' => $project->result,
], fn ($text) => trim($text) !== '');
?>
<article class="container stack project-page">
    <?php if ($preview): ?>
        <p class="preview-banner t-small" role="status"><?= e(lang() === 'fr' ? 'Aperçu : ce projet est un brouillon, invisible pour les visiteurs.' : 'Preview: this project is a draft, hidden from visitors.') ?></p>
    <?php endif ?>

    <a class="project-page__back t-label" href="<?= e(url('projects')) ?>"><?= e(t('projectPage.back')) ?></a>

    <header class="project-page__head">
        <?= component('pill', ['text' => implode(' · ', array_filter([(string) $project->year, t('projects.types.' . $project->type)]))]) ?>
        <h1 class="t-display-l"><?= e($project->title) ?></h1>
        <p class="t-body-l muted project-page__summary"><?= e($project->summary) ?></p>
        <?php if ($project->technologies !== []): ?>
            <div class="tags">
                <?php foreach ($project->technologies as $technology): ?><?= component('tag', ['text' => $technology]) ?><?php endforeach ?>
            </div>
        <?php endif ?>
        <?php if ($project->demoUrl || $project->githubUrl): ?>
            <div class="hero__actions">
                <?php if ($project->demoUrl): ?>
                    <?= component('button', ['label' => t('projectPage.demo'), 'variant' => 'primary', 'href' => $project->demoUrl, 'attributes' => ['target' => '_blank', 'rel' => 'noopener']]) ?>
                <?php endif ?>
                <?php if ($project->githubUrl): ?>
                    <?= component('button', ['label' => t('projectPage.code'), 'variant' => 'secondary', 'href' => $project->githubUrl, 'attributes' => ['target' => '_blank', 'rel' => 'noopener']]) ?>
                <?php endif ?>
            </div>
        <?php endif ?>
    </header>

    <div class="project-page__visual">
        <?php if ($project->imageUrl): ?>
            <img src="<?= e($project->imageUrl) ?>" alt="<?= e($project->title) ?>">
        <?php else: ?>
            <span class="t-small"><?= e(lang() === 'fr' ? '[Visuel du projet]' : '[Project visual]') ?></span>
        <?php endif ?>
    </div>

    <?php if ($cases !== []): ?>
        <section class="grid grid--3" aria-label="<?= e(lang() === 'fr' ? 'Étude de cas' : 'Case study') ?>">
            <?php foreach ($cases as $key => $text): ?>
                <?= component('case-card', ['title' => t('projectPage.' . $key), 'text' => $text]) ?>
            <?php endforeach ?>
        </section>
    <?php endif ?>

    <?php if ($project->gallery !== []): ?>
        <section class="project-page__gallery" aria-label="<?= e(lang() === 'fr' ? 'Galerie' : 'Gallery') ?>">
            <?php foreach ($project->gallery as $i => $image): ?>
                <img src="<?= e($image) ?>" alt="<?= e($project->title . ' — ' . ($i + 1)) ?>" loading="lazy">
            <?php endforeach ?>
        </section>
    <?php endif ?>

    <?php if ($next): ?>
        <section class="project-page__next" aria-labelledby="next-title">
            <h2 class="t-heading-2" id="next-title"><?= e(t('projectPage.next')) ?></h2>
            <div class="grid grid--3">
                <?= component('project-card', [
                    'title' => $next->title,
                    'href' => url('project', ['slug' => $next->slug]),
                    'image' => $next->imageUrl,
                    'tags' => $next->technologies,
                ]) ?>
            </div>
        </section>
    <?php endif ?>
</article>
