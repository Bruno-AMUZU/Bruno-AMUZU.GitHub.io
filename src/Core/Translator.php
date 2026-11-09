<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Textes de l'interface dans la langue de la page, lus dans lang/fr.php et lang/en.php.
 *
 *   t('home.hero.title')                 → « Des sites et des outils web… »
 *   t('cle.du.texte', ['n' => 4])    → remplace {n} dans le texte
 *
 * Une clé absente en anglais est cherchée en français ; absente partout, la clé elle-même
 * est affichée, ce qui rend l'oubli visible.
 */
final class Translator
{
    public const LANGS = ['fr', 'en'];
    public const FALLBACK = 'fr';

    private string $lang = self::FALLBACK;

    /** @var array<string, array<string, mixed>> */
    private array $catalogs = [];

    public function __construct(private readonly string $directory)
    {
    }

    public function setLang(string $lang): void
    {
        $this->lang = in_array($lang, self::LANGS, true) ? $lang : self::FALLBACK;
    }

    public function lang(): string
    {
        return $this->lang;
    }

    /** @param array<string, string|int> $replace */
    public function get(string $key, array $replace = [], ?string $lang = null): string
    {
        $value = $this->raw($key, $lang);
        if (!is_string($value)) {
            return $key;
        }

        foreach ($replace as $name => $replacement) {
            $value = str_replace('{' . $name . '}', (string) $replacement, $value);
        }

        return $value;
    }

    /** Valeur brute (texte, liste ou groupe de textes), null si absente. */
    public function raw(string $key, ?string $lang = null): mixed
    {
        $lang ??= $this->lang;

        return $this->find($this->catalog($lang), $key)
            ?? ($lang !== self::FALLBACK ? $this->find($this->catalog(self::FALLBACK), $key) : null);
    }

    /** @param array<string, mixed> $catalog */
    private function find(array $catalog, string $key): mixed
    {
        $value = $catalog;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return null;
            }
            $value = $value[$part];
        }

        return $value;
    }

    /** @return array<string, mixed> */
    private function catalog(string $lang): array
    {
        return $this->catalogs[$lang] ??= (function () use ($lang): array {
            $file = $this->directory . '/' . $lang . '.php';

            return is_file($file) ? (array) require $file : [];
        })();
    }
}
