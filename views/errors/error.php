<?php
/**
 * Page d'erreur générique (403, 404, 405, 500…).
 *
 * @var int            $status
 * @var Throwable|null $exception détail technique, uniquement en développement
 */
$message = $status === 404 ? t('notFound.title') : t('errors.' . (in_array($status, [403, 405], true) ? $status : 500));
?>
<h1><?= e($status) ?></h1>
<p><?= e($message) ?></p>
<p><a href="<?= e(url('home')) ?>"><?= e(t('notFound.cta')) ?></a></p>

<?php if ($exception !== null): ?>
    <pre><?= e($exception::class . ': ' . $exception->getMessage() . "\n" . $exception->getFile() . ':' . $exception->getLine() . "\n\n" . $exception->getTraceAsString()) ?></pre>
<?php endif ?>
