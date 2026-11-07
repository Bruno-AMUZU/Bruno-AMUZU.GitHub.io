<?php
/**
 * Galerie des composants (développement uniquement). Chaque composant ajouté y est présenté.
 */
$section = fn (string $title) => '<h2 class="t-heading-2">' . e($title) . '</h2>';
?>
<div class="container sg">
    <h1 class="t-heading-1">Composants</h1>
    <p class="t-body-l muted">Page de développement : chaque composant de <code>design/components.md</code>, dans le thème courant.</p>
    <div class="sg-row"><?= component('theme-toggle') ?> <span class="t-small muted">Changer de thème</span></div>

    <?= $section('Button') ?>
    <div class="sg-row">
        <?= component('button', ['label' => 'Me contacter', 'variant' => 'primary', 'href' => '#']) ?>
        <?= component('button', ['label' => 'Voir les projets', 'variant' => 'secondary', 'href' => '#']) ?>
        <?= component('button', ['label' => 'Envoyer le message', 'variant' => 'inverse', 'type' => 'submit']) ?>
        <?= component('button', ['label' => 'Désactivé', 'variant' => 'primary', 'disabled' => true]) ?>
        <?= component('button', ['label' => 'Avec icône', 'variant' => 'secondary', 'icon' => 'arrow']) ?>
    </div>

    <?= $section('IconButton') ?>
    <div class="sg-row">
        <?php foreach (['moon', 'sun', 'close', 'send', 'menu', 'chat', 'arrow', 'edit', 'trash', 'play', 'grip', 'plus'] as $name): ?>
            <?= component('icon-button', ['icon' => $name, 'label' => $name]) ?>
        <?php endforeach ?>
        <?= component('icon-button', ['icon' => 'send', 'label' => 'inv', 'variant' => 'inv']) ?>
        <?= component('icon-button', ['icon' => 'chat', 'label' => 'accent', 'variant' => 'accent']) ?>
    </div>

    <?= $section('Pill · Tag') ?>
    <div class="sg-row">
        <?= component('pill', ['text' => '2026 · Site']) ?>
        <?= component('pill', ['text' => t('home.hero.status'), 'variant' => 'status']) ?>
        <?= component('pill', ['text' => 'Context engineering', 'variant' => 'accent']) ?>
        <?= component('tag', ['text' => 'PHP']) ?>
        <?= component('tag', ['text' => 'PostgreSQL']) ?>
    </div>

    <?= $section('TechChip') ?>
    <ul class="stack sg-chips" role="list">
        <?= component('tech-chip', ['name' => 'PHP 8', 'tech' => 'php']) ?>
        <?= component('tech-chip', ['name' => 'GitHub', 'tech' => 'github']) ?>
        <?= component('tech-chip', ['name' => 'Claude Code', 'mono' => 'CC']) ?>
    </ul>

    <?= $section('Field') ?>
    <div class="grid grid--2 sg-narrow">
        <?= component('field', ['name' => 'name', 'label' => t('form.name'), 'placeholder' => t('form.namePlaceholder')]) ?>
        <?= component('field', ['name' => 'email', 'type' => 'email', 'label' => t('form.email'), 'value' => 'pas-un-email', 'error' => t('form.errors.email')]) ?>
    </div>
    <div class="sg-narrow">
        <?= component('field', ['name' => 'message', 'type' => 'textarea', 'label' => t('form.message'), 'placeholder' => t('form.messagePlaceholder')]) ?>
    </div>

    <?= $section('ChoiceChips') ?>
    <?= component('choice-chips', ['name' => 'type', 'legend' => t('form.type'), 'options' => array_combine(t_list('form.types'), t_list('form.types')), 'selected' => 'Application']) ?>
    <?= component('choice-chips', ['label' => 'Filtres', 'links' => [
        ['label' => 'Tous', 'href' => '#', 'active' => true],
        ['label' => 'Sites', 'href' => '#'],
        ['label' => 'IA', 'href' => '#'],
    ]]) ?>
</div>

<div class="container sg">
    <h2 class="t-heading-2">PageHeader · SectionHeader · LangSwitch · Marque</h2>
    <?= component('page-header', ['title' => t('projectsPage.title'), 'intro' => t('projectsPage.intro')]) ?>
    <?= component('section-header', ['title' => t('home.projects.title'), 'linkLabel' => t('home.projects.link'), 'linkHref' => '#']) ?>
    <div class="sg-row"><?= component('lang-switch') ?> <?= component('marque') ?></div>
</div>
