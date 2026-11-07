<?php

declare(strict_types=1);

namespace App\Controller\Admin\Resource;

/**
 * Un champ du formulaire d'édition d'un type de contenu.
 *
 *   Field::text('slug', 'Slug')->required()->help('…')
 *   Field::textarea('summary', 'Résumé')->translated()
 *   Field::select('type', 'Type', ['site' => 'Site', 'app' => 'Application'])
 *
 * Le contrôleur générique (ResourceController) s'en sert pour afficher, lire et valider le formulaire.
 */
final class Field
{
    public bool $translated = false;
    public bool $required = false;
    public ?string $help = null;
    public ?int $max = null;
    public ?string $placeholder = null;

    /** Panneau de la colonne latérale (Informations, Stack, Médias…) ; null = colonne principale. */
    public ?string $panel = null;

    /** @param array<string, string> $options valeur → libellé (select) */
    private function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly array $options = [],
    ) {
    }

    public static function text(string $name, string $label): self { return new self($name, $label, 'text'); }
    public static function textarea(string $name, string $label): self { return new self($name, $label, 'textarea'); }
    /** Texte long mis en forme (Markdown) : articles du Lab. */
    public static function markdown(string $name, string $label): self { return new self($name, $label, 'markdown'); }
    public static function url(string $name, string $label): self { return new self($name, $label, 'url'); }
    public static function email(string $name, string $label): self { return new self($name, $label, 'email'); }
    public static function number(string $name, string $label): self { return new self($name, $label, 'number'); }
    public static function date(string $name, string $label): self { return new self($name, $label, 'date'); }
    /** Mois et année (parcours) : stocké au 1er du mois. */
    public static function month(string $name, string $label): self { return new self($name, $label, 'month'); }
    public static function toggle(string $name, string $label): self { return new self($name, $label, 'toggle'); }
    /** Liste de mots (technos d'un projet). */
    public static function tags(string $name, string $label): self { return new self($name, $label, 'tags'); }
    /** Image envoyée : la valeur est l'id du fichier. */
    public static function image(string $name, string $label): self { return new self($name, $label, 'image'); }
    /** Plusieurs images (galerie) : la valeur est une liste d'id de fichiers. */
    public static function gallery(string $name, string $label): self { return new self($name, $label, 'gallery'); }
    /** PDF envoyé : la valeur est l'id du fichier. */
    public static function pdf(string $name, string $label): self { return new self($name, $label, 'pdf'); }

    /** @param array<string, string> $options */
    public static function select(string $name, string $label, array $options): self
    {
        return new self($name, $label, 'select', $options);
    }

    public function translated(): self { $this->translated = true; return $this; }
    public function required(): self { $this->required = true; return $this; }
    public function help(string $help): self { $this->help = $help; return $this; }
    public function max(int $max): self { $this->max = $max; return $this; }
    public function placeholder(string $text): self { $this->placeholder = $text; return $this; }
    public function in(string $panel): self { $this->panel = $panel; return $this; }

    public function isFile(): bool
    {
        return in_array($this->type, ['image', 'gallery', 'pdf'], true);
    }
}
