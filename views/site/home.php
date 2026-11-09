<?php
/**
 * Accueil (design/pages/accueil.md) : Hero, Services, Stack, Projets, Lab, et la navigation de section.
 * Interdits : carte back-office, carte « Pour qui », projet vedette, section Contact, CV, téléphone, photo.
 *
 * @var array{status: string, title: string, subtitle: string} $hero
 * @var bool $available
 * @var list<App\Model\Entity\Service> $services
 * @var array<string, list<App\Model\Entity\Skill>> $stack
 * @var list<App\Model\Entity\Project> $projects
 * @var list<App\Model\Entity\Article> $articles
 */

// Navigation de section : seulement les sections affichées (une section vide n'est pas rendue).
$sections = array_values(array_filter([
    ['id' => 'accueil', 'label' => t('common.nav.home'), 'shown' => true],
    ['id' => 'services', 'label' => t('common.nav.services'), 'shown' => $services !== []],
    ['id' => 'stack', 'label' => t('common.nav.stack'), 'shown' => $stack !== []],
    ['id' => 'projets', 'label' => t('common.nav.projects'), 'shown' => $projects !== []],
    ['id' => 'lab', 'label' => t('common.nav.lab'), 'shown' => $articles !== []],
], fn (array $section): bool => $section['shown']));
?>
<?= component('section-nav', ['items' => $sections]) ?>

<section class="container" id="accueil" aria-labelledby="hero-title">
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

<?php if ($articles !== []): ?>
    <section class="container" id="lab" aria-labelledby="lab-title">
        <div class="lab-section">
            <div class="section-header">
                <h2 class="t-heading-1" id="lab-title"><?= e(t('home.lab.title')) ?></h2>
                <a class="section-header__link t-label" href="<?= e(url('lab')) ?>"><?= e(t('home.lab.link')) ?></a>
            </div>
            <ul class="grid grid--3" role="list">
                <?php foreach ($articles as $article): ?>
                    <li><?= component('lab-card', ['article' => $article, 'inverted' => true]) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    </section>
<?php endif ?>
