<?php
/**
 * TechChip — ligne d'une carte Stack : logo officiel + nom (components.md → TechChip).
 * Sans logo officiel (catégorie IA) : monogramme sur un carré accent.
 *
 * @var string      $name
 * @var string|null $tech     clé du logo (public/assets/img/tech/{tech}.svg)
 * @var string|null $mono     monogramme (« CC », « MCP »…)
 * @var bool        $inverted dans une carte inversée
 */
$logo = !empty($tech) ? tech_logo($tech) : '';
?>
<li class="<?= e(classes('tech-chip', !empty($inverted) ? 'tech-chip--inv' : null)) ?>">
    <?php if ($logo !== ''): ?>
        <span class="tech-chip__logo"><?= $logo ?></span>
    <?php else: ?>
        <span class="tech-chip__mono t-caption" aria-hidden="true"><?= e(!empty($mono) ? $mono : mb_strtoupper(mb_substr($name, 0, 2))) ?></span>
    <?php endif ?>
    <span class="t-body"><?= e($name) ?></span>
</li>
