<?php
/**
 * SectionNav — navigation entre les sections d'une page longue (components.md → SectionNav).
 *
 * Grand écran : rail de traits à droite, le nom à droite du trait ; la section en cours a un trait accent
 * et garde son nom affiché.
 * Petit écran : pastille flottante en bas, qui affiche la section en cours et ouvre la liste des sections.
 * Ce sont de vrais liens d'ancre : sans JavaScript, ils fonctionnent ; assets/js/section-nav.js
 * ajoute la section en cours (aria-current) et l'ouverture de la liste.
 *
 * @var list<array{id: string, label: string}> $items sections présentes sur la page, dans l'ordre
 */
if (count($items) < 2) {
    return;
}
?>
<nav class="section-nav" aria-label="<?= e(t('common.sectionNav.label')) ?>" data-section-nav>
    <ul class="section-rail" role="list">
        <?php foreach ($items as $item): ?>
            <li>
                <a class="section-rail__link" href="#<?= e($item['id']) ?>" data-section-link="<?= e($item['id']) ?>">
                    <span class="section-rail__dash" aria-hidden="true"></span>
                    <span class="section-rail__label t-label"><?= e($item['label']) ?></span>
                </a>
            </li>
        <?php endforeach ?>
    </ul>

    <div class="section-pill">
        <ul class="section-pill__list" id="section-pill-list" role="list" hidden>
            <?php foreach ($items as $item): ?>
                <li><a class="section-pill__link t-body" href="#<?= e($item['id']) ?>" data-section-link="<?= e($item['id']) ?>"><?= e($item['label']) ?></a></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="section-pill__button t-label" aria-expanded="false" aria-controls="section-pill-list" data-section-toggle>
            <span class="section-pill__dot" aria-hidden="true"></span>
            <span class="sr-only"><?= e(t('common.sectionNav.pick')) ?> :</span>
            <span data-section-current><?= e($items[0]['label']) ?></span>
            <?= icon('chevron-down', null, 16) ?>
        </button>
    </div>
</nav>
