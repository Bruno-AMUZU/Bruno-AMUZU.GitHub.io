<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Affiche une vue de views/ à l'intérieur d'un gabarit de views/layouts/.
 *
 *   View::render('site/home', ['lang' => 'fr'])
 *   → views/site/home.php, inséré dans views/layouts/site.php
 *
 * Les clés du tableau de données deviennent des variables dans la vue et dans le gabarit
 * ($lang…). Le gabarit reçoit en plus $content : le HTML déjà produit par la vue.
 */
final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = [], string $layout = 'site'): string
    {
        $content = self::capture(self::path($view), $data);

        return self::capture(self::path('layouts/' . $layout), [...$data, 'content' => $content]);
    }

    /**
     * Morceau de page sans gabarit : un composant de views/components/ (bouton, carte…).
     *
     * @param array<string, mixed> $data
     */
    public static function partial(string $view, array $data = []): string
    {
        return self::capture(self::path($view), $data);
    }

    /** 'site/home' → chemin absolu de views/site/home.php */
    private static function path(string $name): string
    {
        // Seuls lettres, chiffres, tirets et « / » : empêche de remonter avec « ../ ».
        if (!preg_match('#^[a-z0-9_-]+(/[a-z0-9_-]+)*$#', $name)) {
            throw new InvalidArgumentException("Nom de vue invalide : {$name}");
        }

        $file = ROOT_PATH . '/views/' . $name . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("Vue introuvable : views/{$name}.php");
        }

        return $file;
    }

    /**
     * Exécute le fichier et renvoie ce qu'il a affiché, au lieu de l'envoyer au navigateur.
     * Méthode statique : la vue n'a accès qu'aux variables de $__data, pas à l'objet.
     *
     * @param array<string, mixed> $__data
     */
    private static function capture(string $__file, array $__data): string
    {
        extract($__data, EXTR_SKIP);
        ob_start();

        try {
            require $__file;

            return (string) ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean(); // on jette la page à moitié produite
            throw $e;
        }
    }
}
