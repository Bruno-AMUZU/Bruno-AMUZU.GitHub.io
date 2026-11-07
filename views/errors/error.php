<?php
/**
 * Page d'erreur générique (403, 404, 405, 500…).
 *
 * @var int            $status
 * @var Throwable|null $exception détail technique, uniquement en développement
 */
$fr = ($lang ?? 'fr') === 'fr';
$messages = [
    403 => $fr ? 'Accès refusé.' : 'Access denied.',
    404 => $fr ? 'Cette page n\'existe pas.' : 'This page does not exist.',
    405 => $fr ? 'Méthode non autorisée.' : 'Method not allowed.',
    500 => $fr ? 'Une erreur est survenue. Réessayez dans un instant.' : 'Something went wrong. Please try again shortly.',
];
?>
<h1><?= e($status) ?></h1>
<p><?= e($messages[$status] ?? $messages[500]) ?></p>
<p><a href="/<?= e($lang ?? 'fr') ?>"><?= $fr ? 'Retour à l\'accueil' : 'Back to home' ?></a></p>

<?php if ($exception !== null): ?>
    <pre><?= e($exception::class . ': ' . $exception->getMessage() . "\n" . $exception->getFile() . ':' . $exception->getLine() . "\n\n" . $exception->getTraceAsString()) ?></pre>
<?php endif ?>
