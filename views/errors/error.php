<?php
/**
 * Page d'erreur (design/pages/404.md) : carte surface centrée, bloc accent avec le code
 * (« 404 » en display-xl couleur onAccent : l'accent reste une surface), titre, texte, bouton accueil.
 * Sert aussi pour 403, 405 et 500, avec le message correspondant.
 *
 * @var int            $status
 * @var Throwable|null $exception détail technique, uniquement en développement
 */
$is404 = $status === 404;
$heading = $is404 ? t('notFound.title') : t('errors.' . (in_array($status, [403, 405], true) ? $status : 500));
?>
<div class="container page-top">
    <section class="error-page" aria-labelledby="error-title">
        <div class="error-page__code t-display-xl" aria-hidden="true"><?= e($status) ?></div>
        <h1 class="t-heading-1" id="error-title"><?= e($heading) ?></h1>
        <?php if ($is404): ?>
            <p class="t-body-l muted"><?= e(t('notFound.text')) ?></p>
        <?php endif ?>
        <?= component('button', ['label' => t('notFound.cta'), 'variant' => 'primary', 'href' => url('home')]) ?>

        <?php if ($exception !== null): ?>
            <pre class="error-page__debug t-code"><?= e($exception::class . ': ' . $exception->getMessage() . "\n" . $exception->getFile() . ':' . $exception->getLine() . "\n\n" . $exception->getTraceAsString()) ?></pre>
        <?php endif ?>
    </section>
</div>
