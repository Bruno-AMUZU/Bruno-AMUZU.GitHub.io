<?php
/**
 * AdminTopbar — titre de page heading-1/mobile + actions à droite (components.md → Back-office).
 * Variante édition : lien retour au-dessus du titre ; les actions passent à la ligne en mobile.
 *
 * @var string      $title
 * @var string|null $backLabel
 * @var string|null $backHref
 * @var string      $actions HTML des boutons (déjà échappé)
 */
?>
<header class="admin-topbar">
    <div class="admin-topbar__titles">
        <?php if (!empty($backHref)): ?>
            <a class="admin-topbar__back t-label muted" href="<?= e($backHref) ?>"><?= e($backLabel ?? '←') ?></a>
        <?php endif ?>
        <h1 class="t-heading-1 t-heading-1-mobile"><?= e($title) ?></h1>
    </div>
    <?php if (!empty($actions)): ?>
        <div class="admin-topbar__actions"><?= $actions ?></div>
    <?php endif ?>
</header>
