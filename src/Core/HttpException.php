<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Erreur HTTP volontaire (404 page introuvable, 403 interdit, 405 méthode non autorisée…).
 * Lancée n'importe où, elle est transformée en page d'erreur par le point d'entrée.
 */
final class HttpException extends RuntimeException
{
    /** @param array<string, string> $headers */
    public function __construct(
        public readonly int $status,
        public readonly array $headers = [],
        string $message = '',
    ) {
        parent::__construct($message !== '' ? $message : "Erreur HTTP {$status}", $status);
    }
}
