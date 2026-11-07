<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Associe une URL et une méthode HTTP à l'action qui doit y répondre.
 *
 * Les chemins peuvent contenir des paramètres :
 *   '/{lang}/projets/{slug}'   → n'importe quelle valeur (sans « / »)
 *   '/{lang:fr|en}'            → valeur limitée par une expression régulière
 *
 * L'action est soit [NomDuControleur::class, 'methode'], soit une fonction.
 * Les paramètres de l'URL lui sont passés par leur nom.
 */
final class Router
{
    /** @var array<string, array<string, callable|array{class-string, string}>> méthode → [regex → action] */
    private array $routes = [];

    /** Le conteneur fabrique les contrôleurs avec leurs dépendances (repositories…). */
    public function __construct(private readonly Container $container = new Container())
    {
    }

    public function get(string $path, callable|array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, callable|array $action): void
    {
        $this->add('POST', $path, $action);
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = $method === 'HEAD' ? 'GET' : $method;
        $path = $this->normalize(parse_url($uri, PHP_URL_PATH) ?: '/');

        foreach ($this->routes[$method] ?? [] as $regex => $action) {
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($action, $params);
                return;
            }
        }

        // L'URL existe avec une autre méthode (ex. GET sur un formulaire POST) : 405, sinon 404.
        $allowed = $this->allowedMethods($path);
        if ($allowed !== []) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $allowed));
            echo 'Méthode non autorisée';
            return;
        }

        http_response_code(404);
        echo 'Page introuvable';
    }

    private function add(string $method, string $path, callable|array $action): void
    {
        $this->routes[$method][$this->compile($this->normalize($path))] = $action;
    }

    /** Transforme '/{lang:fr|en}/projets/{slug}' en expression régulière avec groupes nommés. */
    private function compile(string $path): string
    {
        $regex = preg_replace_callback(
            '#\{(\w+)(?::([^}]+))?\}#',
            fn (array $m): string => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $path,
        );

        return '#^' . $regex . '$#';
    }

    /** '/fr/projets/' et '/fr/projets' désignent la même page. */
    private function normalize(string $path): string
    {
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    /** @param array<string, string> $params */
    private function call(callable|array $action, array $params): void
    {
        if (is_array($action)) {
            [$class, $method] = $action;
            $action = [$this->container->get($class), $method];
        }

        $action(...$params);
    }

    /** @return list<string> */
    private function allowedMethods(string $path): array
    {
        $allowed = [];
        foreach ($this->routes as $method => $routes) {
            foreach (array_keys($routes) as $regex) {
                if (preg_match($regex, $path)) {
                    $allowed[] = $method;
                    break;
                }
            }
        }

        return $allowed;
    }
}
