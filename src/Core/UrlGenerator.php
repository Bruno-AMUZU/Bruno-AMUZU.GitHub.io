<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;

/**
 * Fabrique les URL à partir du nom des pages, dans la bonne langue.
 *
 *   url('projects')                          → /fr/projets   (page en français)
 *   url('project', ['slug' => 'brume'], 'en') → /en/projects/brume
 *
 * Connaît aussi la page en cours, pour que le sélecteur FR · EN mène à la même page dans l'autre langue.
 */
final class UrlGenerator
{
    /** @var array<string, array<string, string>> nom → [langue → chemin] */
    private array $routes = [];

    private ?string $currentName = null;

    /** @var array<string, string> */
    private array $currentParams = [];

    public function __construct(private readonly Translator $translator)
    {
    }

    public function register(string $name, string $lang, string $path): void
    {
        $this->routes[$name][$lang] = $path;
    }

    /** @param array<string, string> $params */
    public function setCurrent(?string $name, array $params): void
    {
        $this->currentName = $name;
        $this->currentParams = $params;
    }

    /** @param array<string, string|int> $params */
    public function to(string $name, array $params = [], ?string $lang = null): string
    {
        $lang ??= $this->translator->lang();
        $path = $this->routes[$name][$lang] ?? $this->routes[$name]['*']
            ?? throw new InvalidArgumentException("Page inconnue : {$name} ({$lang})");

        $url = preg_replace_callback(
            '#\{(\w+)(?::[^}]+)?\}#',
            fn (array $m): string => isset($params[$m[1]])
                ? rawurlencode((string) $params[$m[1]])
                : throw new InvalidArgumentException("Paramètre manquant pour {$name} : {$m[1]}"),
            $path,
        );

        return $url === '' ? '/' : $url;
    }

    /** La page en cours dans une autre langue (accueil de cette langue si la page n'existe pas). */
    public function switchTo(string $lang): string
    {
        if ($this->currentName !== null && isset($this->routes[$this->currentName][$lang])) {
            return $this->to($this->currentName, $this->currentParams, $lang);
        }

        return '/' . $lang;
    }

    public function currentName(): ?string
    {
        return $this->currentName;
    }
}
