<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Associe une URL et une méthode HTTP à l'action qui doit y répondre.
 *
 *   $router->page('projects', ['fr' => '/fr/projets', 'en' => '/en/projects'], [ProjectController::class, 'index']);
 *   $router->post('/fr/contact', [ContactController::class, 'send'], 'contact.send');
 *
 * Les chemins peuvent contenir des paramètres :
 *   '/fr/projets/{slug}'   → n'importe quelle valeur (sans « / »)
 *   '/{lang:fr|en}'        → valeur limitée par une expression régulière
 *
 * L'action est soit [NomDuControleur::class, 'methode'], soit une fonction. Les paramètres de
 * l'URL lui sont passés par leur nom ($lang, $slug…). Elle renvoie une Response, ou une chaîne
 * (HTML) transformée en Response.
 *
 * Avant l'action, des « middlewares » peuvent vérifier la requête (ex. être connecté au back-office) :
 * ils renvoient une Response pour l'arrêter (redirection…), ou null pour la laisser passer.
 */
final class Router
{
    /** {nom} ou {nom:regex} ; la regex peut elle-même contenir des accolades ({nom:\d{4}}). */
    public const PLACEHOLDER = '#\{(\w+)(?::((?:[^{}]|\{[^{}]*\})+))?\}#';

    /** @var list<array{method: string, regex: string, action: callable|array, defaults: array<string, string>, name: ?string, middleware: list<class-string>}> */
    private array $routes = [];

    /** @var list<class-string> middlewares ajoutés aux routes déclarées dans le groupe en cours */
    private array $groupMiddleware = [];

    /** Le conteneur fabrique les contrôleurs avec leurs dépendances (repositories…). */
    public function __construct(private readonly Container $container = new Container())
    {
    }

    public function get(string $path, callable|array $action, ?string $name = null): void
    {
        $this->add('GET', $path, $action, [], $name);
    }

    public function post(string $path, callable|array $action, ?string $name = null): void
    {
        $this->add('POST', $path, $action, [], $name);
    }

    /**
     * Page publique présente dans chaque langue, avec un chemin traduit.
     *
     * @param array<string, string> $paths langue → chemin
     */
    public function page(string $name, array $paths, callable|array $action, string $method = 'GET'): void
    {
        foreach ($paths as $lang => $path) {
            $this->add($method, $path, $action, ['lang' => $lang], $name);
        }
    }

    /**
     * Les routes déclarées dans $routes passent d'abord par les middlewares donnés.
     *
     * @param list<class-string> $middleware
     * @param callable(self): void $routes
     */
    public function group(array $middleware, callable $routes): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = [...$previous, ...$middleware];
        $routes($this);
        $this->groupMiddleware = $previous;
    }

    public function dispatch(Request $request): Response
    {
        $path = $this->normalize($request->path);

        foreach ($this->routes as $route) {
            if ($route['method'] === $request->method && preg_match($route['regex'], $path, $matches)) {
                $params = [...$route['defaults'], ...array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)];

                return $this->run($route, $params, $request);
            }
        }

        // L'URL existe avec une autre méthode (ex. GET sur un formulaire POST) : 405, sinon 404.
        $allowed = $this->allowedMethods($path);
        if ($allowed !== []) {
            throw new HttpException(405, ['Allow' => implode(', ', $allowed)]);
        }

        throw new HttpException(404);
    }

    /**
     * @param array<string, string> $defaults
     */
    private function add(string $method, string $path, callable|array $action, array $defaults, ?string $name): void
    {
        $path = $this->normalize($path);
        $this->routes[] = [
            'method' => $method,
            'regex' => $this->compile($path),
            'action' => $action,
            'defaults' => $defaults,
            'name' => $name,
            'middleware' => $this->groupMiddleware,
        ];

        if ($name !== null) {
            $this->container->get(UrlGenerator::class)->register($name, $defaults['lang'] ?? '*', $path);
        }
    }

    /**
     * @param array{action: callable|array, name: ?string, middleware: list<class-string>} $route
     * @param array<string, string> $params
     */
    private function run(array $route, array $params, Request $request): Response
    {
        // La langue de la page, si elle en a une, sert aux traductions et aux liens.
        $lang = $params['lang'] ?? null;
        if (is_string($lang)) {
            $this->container->get(Translator::class)->setLang($lang);
        }
        $this->container->get(UrlGenerator::class)->setCurrent(
            $route['name'],
            array_diff_key($params, ['lang' => true]),
        );

        foreach ($route['middleware'] as $class) {
            $stop = $this->container->get($class)->handle($request);
            if ($stop instanceof Response) {
                return $stop;
            }
        }

        $action = $route['action'];
        if (is_array($action)) {
            [$class, $method] = $action;
            $action = [$this->container->get($class), $method];
        }

        $result = $action(...$this->acceptedParams($action, $params));

        return $result instanceof Response ? $result : Response::html((string) $result);
    }

    /**
     * Ne garde que les paramètres que l'action déclare : une page traduite reçoit toujours « lang »,
     * mais une action qui n'en a pas besoin n'a pas à le déclarer.
     *
     * @param array<string, string> $params
     * @return array<string, string>
     */
    private function acceptedParams(callable $action, array $params): array
    {
        $reflection = is_array($action)
            ? new \ReflectionMethod($action[0], $action[1])
            : new \ReflectionFunction(\Closure::fromCallable($action));

        $names = [];
        foreach ($reflection->getParameters() as $param) {
            if ($param->isVariadic()) {
                return $params;
            }
            $names[$param->getName()] = true;
        }

        return array_intersect_key($params, $names);
    }

    /** Transforme '/{lang:fr|en}/projets/{slug}' en expression régulière avec groupes nommés. */
    private function compile(string $path): string
    {
        $regex = preg_replace_callback(
            self::PLACEHOLDER,
            fn (array $m): string => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $path,
        );

        return '#^' . $regex . '$#u';
    }

    /** '/fr/projets/' et '/fr/projets' désignent la même page. */
    private function normalize(string $path): string
    {
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    /** @return list<string> */
    private function allowedMethods(string $path): array
    {
        $allowed = [];
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path)) {
                $allowed[] = $route['method'];
            }
        }

        return array_values(array_unique($allowed));
    }
}
