<?php
/**
 * Accueil (design/pages/accueil.md) : Hero, Services, Stack, Projets.
 * Interdits : carte back-office, carte « Pour qui », projet vedette, section Contact, CV, téléphone, photo.
 *
 * @var array{status: string, title: string, subtitle: string} $hero
 * @var bool $available
 * @var list<App\Model\Entity\Service> $services
 * @var array<string, list<App\Model\Entity\Skill>> $stack
 * @var list<App\Model\Entity\Project> $projects
 */
?>
<section class="container" aria-labelledby="hero-title">
    <div class="hero">
        <?php if ($available && $hero['status'] !== ''): ?>
            <?= component('pill', ['text' => $hero['status'], 'variant' => 'status']) ?>
        <?php endif ?>
        <h1 class="hero__title t-display-xl" id="hero-title"><?= e($hero['title']) ?></h1>
        <div class="hero__bottom">
            <p class="t-body-l muted"><?= e($hero['subtitle']) ?></p>
            <div class="hero__actions">
                <?= component('button', ['label' => t('home.hero.ctaPrimary'), 'variant' => 'primary', 'href' => has_route('contact') ? url('contact') : 'mailto:' . setting('contact_email')]) ?>
                <?= component('button', ['label' => t('home.hero.ctaSecondary'), 'variant' => 'secondary', 'href' => has_route('projects') ? url('projects') : '#projets']) ?>
            </div>
        </div>
    </div>
</section>

<?php if ($services !== []): ?>
    <section class="container" id="services" aria-labelledby="services-title">
        <?= component('section-header', ['title' => t('home.services.title'), 'id' => 'services-title']) ?>
        <div class="grid grid--3">
            <?php foreach ($services as $service): ?>
                <?= component('service-card', [
                    'illustration' => $service->illustration,
                    'title' => $service->title,
                    'text' => $service->description,
                    'inverted' => $service->isInverted(),
                ]) ?>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>

<?php if ($stack !== []): ?>
    <section class="container" id="stack" aria-labelledby="stack-title">
        <?= component('section-header', ['title' => t('home.stack.title'), 'id' => 'stack-title']) ?>
        <div class="grid grid--stack">
            <?php foreach ($stack as $category => $skills): ?>
                <?= component('stack-card', [
                    'title' => t('skills.categories.' . $category),
                    'items' => array_map(fn ($skill) => $skill->chip(), $skills),
                    'inverted' => $category === 'ai',
                ]) ?>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>

<?php if ($projects !== []): ?>
    <section class="container" id="projets" aria-labelledby="projects-title">
        <?= component('section-header', [
            'title' => t('home.projects.title'),
            'id' => 'projects-title',
            'linkLabel' => t('home.projects.link'),
            'linkHref' => has_route('projects') ? url('projects') : null,
        ]) ?>
        <div class="grid grid--3">
            <?php foreach ($projects as $project): ?>
                <?= component('project-card', [
                    'title' => $project->title,
                    'href' => has_route('project') ? url('project', ['slug' => $project->slug]) : '#projets',
                    'image' => $project->imageUrl,
                    'tags' => $project->technologies,
                ]) ?>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>
