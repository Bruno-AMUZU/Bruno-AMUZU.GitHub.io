<?php
/**
 * CaseCard — bloc de l'étude de cas (components.md → CaseCard) : titre heading-2 + texte body muted.
 *
 * @var string $title
 * @var string $text
 */
?>
<article class="case-card">
    <h2 class="t-heading-2"><?= e($title) ?></h2>
    <p class="t-body muted"><?= nl2br(e($text)) ?></p>
</article>
