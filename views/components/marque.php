<?php
/**
 * Marque — symbole « terminal » >b_ + « bruno amuzu » (components.md → Header ; public/assets/img/logo.svg).
 * Le symbole est redessiné ici avec des classes (le <style> du fichier SVG serait bloqué par la CSP) :
 * couleurs fixes color.brand.*, liseré visible en sombre seulement, curseur clignotant sauf prefers-reduced-motion.
 *
 * @var string|null $href    lien (accueil par défaut)
 * @var bool        $inverse sur fond inversé (barre latérale du back-office)
 * @var bool        $text    afficher « bruno amuzu » (oui par défaut)
 */
$href ??= url('home');
?>
<a class="<?= e(classes('marque', !empty($inverse) ? 'marque--inv' : null)) ?>" href="<?= e($href) ?>">
    <svg class="marque__symbol" viewBox="0 0 32 32" width="28" height="28" aria-hidden="true" focusable="false">
        <rect class="marque__bg" width="32" height="32" rx="7"/>
        <rect class="marque__edge" x="0.5" y="0.5" width="31" height="31" rx="6.5"/>
        <path class="marque__accent-line" d="M6.5 12 L11.5 17 L6.5 22"/>
        <path class="marque__fg-line" d="M15.5 8.5 V23.5"/>
        <circle class="marque__fg-line" cx="19.2" cy="19.8" r="3.7"/>
        <rect class="marque__cursor" x="24" y="22.2" width="4" height="2.6" rx="0.6"/>
    </svg>
    <?php if ($text ?? true): ?>
        <span class="marque__text t-logo"><?= e(t('common.logo')) ?></span>
    <?php else: ?>
        <span class="sr-only"><?= e(t('common.logo')) ?></span>
    <?php endif ?>
</a>
