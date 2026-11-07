<?php

declare(strict_types=1);

namespace App\Core;

/**
 * La requête HTTP reçue : méthode, chemin, paramètres d'URL, données de formulaire, fichiers.
 * Remplace l'accès direct à $_GET, $_POST, $_FILES et $_SERVER dans les contrôleurs.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query  paramètres d'URL (?page=2)
     * @param array<string, mixed> $body   données de formulaire (POST)
     * @param array<string, mixed> $files  fichiers envoyés
     * @param array<string, mixed> $server informations du serveur
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        private readonly array $query = [],
        private readonly array $body = [],
        private readonly array $files = [],
        private readonly array $server = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        return new self(
            $method === 'HEAD' ? 'GET' : $method,
            parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
        );
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /** Paramètre d'URL texte (les tableaux sont ignorés). */
    public function query(string $key, ?string $default = null): ?string
    {
        $value = $this->query[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /** Champ de formulaire texte, espaces de début et de fin retirés. */
    public function input(string $key, ?string $default = null): ?string
    {
        $value = $this->body[$key] ?? null;

        return is_string($value) ? trim($value) : $default;
    }

    /** @return list<string> champ de formulaire multiple (name="tags[]") */
    public function inputList(string $key): array
    {
        $value = $this->body[$key] ?? [];

        return is_array($value) ? array_values(array_map('trim', array_filter($value, 'is_string'))) : [];
    }

    /** @return array<string, mixed> toutes les données du formulaire */
    public function all(): array
    {
        return $this->body;
    }

    /**
     * Champ texte imbriqué : nested('tr', 'fr', 'title') lit tr[fr][title].
     */
    public function nested(string ...$path): ?string
    {
        $value = $this->body;
        foreach ($path as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return is_string($value) ? trim($value) : null;
    }

    /**
     * Liste imbriquée : nestedList('keep', 'gallery') lit keep[gallery][].
     *
     * @return list<string>
     */
    public function nestedList(string ...$path): array
    {
        $value = $this->body;
        foreach ($path as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return [];
            }
            $value = $value[$key];
        }

        return is_array($value) ? array_values(array_map('trim', array_filter($value, 'is_string'))) : [];
    }

    /**
     * Fichiers d'un champ multiple (<input type="file" name="gallery[]" multiple>), un tableau par fichier.
     *
     * @return list<array<string, mixed>>
     */
    public function files(string $key): array
    {
        $raw = $this->files[$key] ?? null;
        if (!is_array($raw) || !is_array($raw['name'] ?? null)) {
            $single = $this->file($key);

            return $single !== null ? [$single] : [];
        }

        $files = [];
        foreach (array_keys($raw['name']) as $i) {
            $file = [];
            foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $attribute) {
                $file[$attribute] = $raw[$attribute][$i] ?? null;
            }
            if ((int) $file['error'] !== UPLOAD_ERR_NO_FILE) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /** @return array<string, mixed>|null fichier envoyé (structure de $_FILES), null si absent */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function isSecure(): bool
    {
        return ($this->server['HTTPS'] ?? 'off') !== 'off'
            || ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return isset($this->server[$key]) ? (string) $this->server[$key] : null;
    }
}
