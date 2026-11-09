<?php

declare(strict_types=1);

namespace App\Core;

use ErrorException;
use Throwable;

/**
 * Transforme toute erreur en page d'erreur propre.
 *
 * - Les avertissements PHP (variable inconnue, etc.) deviennent des exceptions : on ne les ignore pas.
 * - HttpException (404, 403…) : page d'erreur correspondante, rien dans le journal.
 * - Toute autre exception : page 500, détail noté dans storage/logs/ ; le détail technique
 *   n'est affiché qu'en développement (APP_ENV=dev), jamais en production.
 */
final class ErrorHandler
{
    public function __construct(
        private readonly Logger $logger,
        private readonly bool $debug,
    ) {
    }

    public function register(): void
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false; // erreur volontairement masquée par @
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
    }

    public function handle(Throwable $e, Request $request): Response
    {
        $status = $e instanceof HttpException ? $e->status : 500;

        if ($status >= 500) {
            $this->logger->error($e::class . ': ' . $e->getMessage(), [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'url' => $request->method . ' ' . $request->path,
            ]);
        }

        // On jette ce qui a pu être affiché avant l'erreur.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Langue déduite de l'URL (/en/… → anglais), la route n'ayant peut-être pas été trouvée.
        App::get(Translator::class)->setLang(str_starts_with($request->path, '/en') ? 'en' : 'fr');

        try {
            $body = View::render('errors/error', [
                'status' => $status,
                'title' => (string) $status,
                'exception' => $this->debug && $status >= 500 ? $e : null,
                'scripts' => $status === 404 ? ['/assets/js/error-pixels.js'] : [],
            ]);
        } catch (Throwable) {
            $body = "Erreur {$status}"; // la page d'erreur elle-même a échoué : texte brut
        }

        $headers = $e instanceof HttpException ? $e->headers : [];

        return new Response($body, $status, ['Content-Type' => 'text/html; charset=UTF-8', ...$headers]);
    }
}
