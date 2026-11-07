<?php
/**
 * Projets — liste (design/pages/projets.md) : en-tête de page, filtres, grille de ProjectCard identiques.
 *
 * @var list<App\Model\Entity\Project> $projects
 * @var list<array{label: string, href: string, active: bool}> $filters
 */
?>
<div class="container stack page-stack">
    <?= component('page-header', ['title' => t('projectsPage.title'), 'intro' => t('projectsPage.intro')]) ?>

    <?= component('choice-chips', ['label' => lang() === 'fr' ? 'Filtrer les projets' : 'Filter projects', 'links' => $filters]) ?>

    <?php if ($projects === []): ?>
        <p class="t-body-l muted"><?= e(t('projectsPage.empty')) ?></p>
    <?php else: ?>
        <ul class="grid grid--3" role="list">
            <?php foreach ($projects as $project): ?>
                <li>
                    <?= component('project-card', [
                        'title' => $project->title,
                        'href' => has_route('project') ? url('project', ['slug' => $project->slug]) : '#',
                        'image' => $project->imageUrl,
                        'tags' => $project->technologies,
                        'headingLevel' => 'h2',
                    ]) ?>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
