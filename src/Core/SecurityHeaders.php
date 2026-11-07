<?php

declare(strict_types=1);

namespace App\Core;

/**
 * En-têtes HTTP de sécurité ajoutés à chaque page.
 *
 * La Content-Security-Policy n'autorise que nos propres fichiers, plus les vidéos YouTube du Lab.
 * Un script écrit directement dans la page n'est exécuté que s'il porte le « nonce » de la requête
 * (<script nonce="<?= e(csp_nonce()) ?>">) : un script injecté par un attaquant ne l'a pas.
 */
final class SecurityHeaders
{
    private string $nonce;

    public function __construct(private readonly bool $production = false)
    {
        $this->nonce = base64_encode(random_bytes(16));
    }

    public function nonce(): string
    {
        return $this->nonce;
    }

    public function apply(Response $response): Response
    {
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$this->nonce}'",
            "style-src 'self'",
            "img-src 'self' data: https://i.ytimg.com",
            "font-src 'self'",
            "frame-src https://www.youtube-nocookie.com",
            "connect-src 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",   // le site ne peut pas être affiché dans une iframe (clickjacking)
            "base-uri 'self'",
            "object-src 'none'",
        ]);

        $headers = [
            'Content-Security-Policy' => $csp,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        // HSTS : le navigateur n'utilisera plus que HTTPS. Seulement en production, sinon localhost serait bloqué.
        if ($this->production) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            if ($response->header($name) === null) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }
}
