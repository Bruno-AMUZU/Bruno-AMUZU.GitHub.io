<?php
/**
 * CertCard — carte de certification (components.md → CertCard). Toutes identiques.
 *
 * @var string      $date
 * @var string      $title
 * @var string|null $org
 * @var string|null $href lien vers le PDF ou la page du certificat
 */
?>
<article class="cert-card">
    <p class="t-small muted"><?= e($date) ?></p>
    <h3 class="t-heading-3"><?= e($title) ?></h3>
    <?php if (!empty($org)): ?><p class="t-body muted"><?= e($org) ?></p><?php endif ?>
    <?php if (!empty($href)): ?>
        <a class="cert-card__link t-label" href="<?= e($href) ?>" target="_blank" rel="noopener"><?= e(t('about.certifications.link')) ?><span class="sr-only"> — <?= e($title) ?></span></a>
    <?php endif ?>
</article>
