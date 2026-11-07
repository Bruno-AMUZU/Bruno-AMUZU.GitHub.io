<?php
/**
 * ChoiceChips — groupe de choix (components.md → ChoiceChips).
 *
 * Deux usages :
 * - dans un formulaire (« Type de projet ») : boutons radio, $name + $options + $selected ;
 * - comme filtres d'une liste (Projets, Lab) : liens, $links = [['label' => …, 'href' => …, 'active' => bool], …].
 *
 * @var string|null                $name
 * @var array<string, string>      $options  valeur → libellé
 * @var string|null                $selected
 * @var string|null                $legend   titre du groupe (formulaire)
 * @var list<array<string, mixed>> $links
 * @var string|null                $label    libellé accessible de la liste de filtres
 */
?>
<?php if (!empty($links)): ?>
<nav class="<?= e(classes('chips', $class ?? null)) ?>" aria-label="<?= e($label ?? '') ?>">
    <ul class="chips__list" role="list">
        <?php foreach ($links as $link): ?>
            <li><a<?= attrs(['class' => 'chip t-label', 'href' => $link['href'], 'aria-current' => !empty($link['active']) ? 'true' : null]) ?>><?= e($link['label']) ?></a></li>
        <?php endforeach ?>
    </ul>
</nav>
<?php else: ?>
<fieldset class="<?= e(classes('chips', $class ?? null)) ?>">
    <?php if (!empty($legend)): ?><legend class="field__label t-small"><?= e($legend) ?></legend><?php endif ?>
    <div class="chips__list">
        <?php foreach ($options as $value => $optionLabel): ?>
            <label class="chip t-label">
                <input class="chip__input" type="radio" name="<?= e($name) ?>" value="<?= e($value) ?>"<?= (string) $value === (string) ($selected ?? '') ? ' checked' : '' ?>>
                <span><?= e($optionLabel) ?></span>
            </label>
        <?php endforeach ?>
    </div>
</fieldset>
<?php endif ?>
