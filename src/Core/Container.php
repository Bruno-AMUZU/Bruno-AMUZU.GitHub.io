<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

/**
 * Conteneur de dépendances : fabrique les objets et leur fournit ce dont ils ont besoin.
 *
 *   $container->set(PDO::class, fn () => Database::connect());
 *   $container->get(ProjectRepository::class);   // reçoit le PDO dans son constructeur
 *
 * Les classes sans recette explicite sont construites automatiquement en lisant les types
 * de leur constructeur (autowiring). Chaque objet n'est créé qu'une fois par requête.
 */
final class Container
{
    /** @var array<string, Closure(self): object> */
    private array $factories = [];

    /** @var array<string, object> */
    private array $instances = [];

    /** @param Closure(self): object $factory */
    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->instances[$id]);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        return $this->instances[$id] ??= isset($this->factories[$id])
            ? ($this->factories[$id])($this)
            : $this->build($id);
    }

    private function build(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Classe introuvable : {$class}");
        }

        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->get($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new RuntimeException("Impossible de fournir \${$param->getName()} à {$class}");
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}
