<?php
/**
 * À propos (design/pages/a-propos.md). Interdits : barres de progression, pourcentages, emojis.
 *
 * @var string|null $photo
 * @var string|null $cv
 * @var list<App\Model\Entity\TimelineEntry> $timeline
 * @var array<string, list<App\Model\Entity\Skill>> $stack
 * @var list<App\Model\Entity\Certification> $certifications
 */
?>
<section class="container page-top" aria-labelledby="about-title">
    <div class="about-intro">
        <div class="about-intro__text">
            <h1 class="t-display-l" id="about-title"><?= e(t('about.intro.title')) ?></h1>
            <div class="about-intro__story">
                <?php foreach (t_list('about.intro.text') as $i => $paragraph): ?>
                    <p class="<?= $i === 0 ? 't-heading-3' : 't-body-l' ?>"><?= e($paragraph) ?></p>
                <?php endforeach ?>
            </div>
            <div class="hero__actions">
                <?php if ($cv): ?>
                    <?= component('button', ['label' => t('about.intro.cv'), 'variant' => 'primary', 'href' => $cv, 'attributes' => ['download' => 'Bruno-Amuzu-CV.pdf']]) ?>
                <?php endif ?>
                <?= component('button', ['label' => t('about.intro.contact'), 'variant' => 'secondary', 'href' => url('contact')]) ?>
            </div>
        </div>
        <div class="about-intro__photo">
            <?php if ($photo): ?>
                <img src="<?= e($photo) ?>" alt="Bruno Amuzu" width="480" height="600">
            <?php else: ?>
                <span class="t-small muted"><?= e(lang() === 'fr' ? '[Photo à fournir]' : '[Photo to come]') ?></span>
            <?php endif ?>
        </div>
    </div>
</section>

<?php if ($timeline !== []): ?>
    <section class="container" aria-labelledby="timeline-title">
        <?= component('section-header', ['title' => t('about.timeline.title'), 'id' => 'timeline-title']) ?>
        <ol class="timeline" role="list">
            <?php foreach ($timeline as $entry): ?>
                <?= component('timeline-card', [
                    'kind' => t('about.kinds.' . $entry->kind),
                    'period' => $entry->period(lang()),
                    'title' => $entry->title,
                    'org' => $entry->organization,
                    'place' => $entry->place,
                    'text' => $entry->description,
                ]) ?>
            <?php endforeach ?>
        </ol>
    </section>
<?php endif ?>

<?php if ($stack !== []): ?>
    <section class="container" aria-labelledby="skills-title">
        <?= component('section-header', ['title' => t('about.skills.title'), 'id' => 'skills-title']) ?>
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

<?php if ($certifications !== []): ?>
    <section class="container" aria-labelledby="certifications-title">
        <?= component('section-header', ['title' => t('about.certifications.title'), 'id' => 'certifications-title']) ?>
        <ul class="grid grid--3" role="list">
            <?php foreach ($certifications as $certification): ?>
                <li>
                    <?= component('cert-card', [
                        'date' => format_date($certification->obtainedOn, lang(), 'month'),
                        'title' => $certification->title,
                        'org' => $certification->organization,
                        'href' => $certification->link(),
                    ]) ?>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
<?php endif ?>
