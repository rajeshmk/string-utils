<?php

declare(strict_types=1);

namespace Hatchyu\String;

class StrTo
{
    protected static array $realWords = [];

    protected static array $slugableCache = [];

    protected static array $compoundWords = [];

    /**
     * Convert string to `TitleCase`.
     */
    public static function title(string $string): string
    {
        return mb_convert_case(static::realWords($string), MB_CASE_TITLE_SIMPLE, 'UTF-8');
    }

    /**
     * Smart version of ucwords().
     */
    public static function words(string $string): string
    {
        $parts = explode(' ', static::realWords($string));

        $ucWords = array_map(fn ($value) => static::ucfirst($value), $parts);

        return implode(' ', $ucWords);
    }

    /**
     * Alias of words().
     */
    public static function headline(string $string): string
    {
        return static::words($string);
    }

    /**
     * Convert string to snake case.
     */
    public static function snake(string $string): string
    {
        return static::slugable($string, '_');
    }

    public static function kebab(string $string): string
    {
        return static::slugable($string, '-');
    }

    public static function dotted(string $string): string
    {
        return static::slugable($string, '.');
    }

    /**
     * Convert string to StudlyCase.
     */
    public static function studly(string $string): string
    {
        return str_replace(' ', '', static::title($string));
    }

    /**
     * Convert string to camelCase.
     */
    public static function camel(string $string): string
    {
        return static::lcfirst(static::studly($string));
    }

    /**
     * Convert "admin/ModuleName/TestNamespace" to "admin.module-name.test-namespace".
     */
    public static function dotPath(string $path): string
    {
        $parts = explode('/', str_replace('\\', '/', $path));
        $parts = array_map(fn ($value) => static::slugable($value, '-'), $parts);

        return implode('.', $parts);
    }

    /**
     * Convert the given string to upper case.
     */
    public static function upper(string $string): string
    {
        return mb_convert_case($string, MB_CASE_UPPER_SIMPLE, 'UTF-8');
    }

    /**
     * Convert the given string to lower case.
     */
    public static function lower(string $string): string
    {
        return mb_convert_case($string, MB_CASE_LOWER_SIMPLE, 'UTF-8');
    }

    public static function ucfirst(string $string): string
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    public static function lcfirst(string $string): string
    {
        return static::lower(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    /**
     * Returns the portion of the string specified by the start and length parameters.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Laravel's Str::limit() won't preserve words. Use this function in such cases.
     */
    public static function slug(string $string, int $maxLength = 120, string $lang = 'en'): string
    {
        // Replace @ with the word 'at'
        $string = str_replace('@', '-at-', $string);

        $string = static::transliterateToEnglish($string, $lang);

        // Keep lower case words separated by SPACE itself
        $string = static::slugable($string, ' ');

        // Keep `$maxLength` - Wordwrap separated by '@'
        $wrappedText = wordwrap($string, $maxLength, '@', true);
        $string = str_replace(' ', '-', current(explode('@', $wrappedText)));

        // Remove non-alphanumeric characters
        if ($lang === 'en') {
            $string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);
        }

        return $string;
    }

    /**
     * Set package-wide compound words that should be preserved during splitting.
     */
    public static function setCompoundWords(array $compoundWords): void
    {
        $compoundWords = array_values(array_unique(array_filter($compoundWords)));
        usort($compoundWords, static fn ($left, $right) => strlen($right) <=> strlen($left));

        static::$compoundWords = $compoundWords;
        static::$realWords = [];
        static::$slugableCache = [];
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private static function realWords(string $string): string
    {
        $key = md5($string) . '|' . md5(implode('|', static::$compoundWords));

        if (isset(static::$realWords[$key])) {
            return static::$realWords[$key];
        }

        [$string, $placeholderMap] = static::protectCompoundWords($string);

        // Replace punctuation and separators with spaces, while keeping word characters intact.
        $string = preg_replace('/[\W|_]+/u', ' ', $string);

        // Split acronym-to-word boundaries: "XMLParser" => "XML Parser".
        $string = preg_replace('/([A-Z]{2,})([A-Z][a-z])/', '$1 $2', $string);

        // Split lower-to-upper camelCase transitions: "helloWorld" => "hello World".
        $string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);

        // Split digit-to-letter transitions: "OAuth2Token" => "OAuth2 Token".
        $string = preg_replace('/(\d)([A-Za-z])/', '$1 $2', $string);

        $string = trim(preg_replace('/\s+/', ' ', $string));

        return static::$realWords[$key] = static::restoreCompoundWords($string, $placeholderMap);
    }

    private static function slugable(string $string, string $separator = '-'): string
    {
        if (ctype_lower($string)) {
            return $string;
        }

        if (! isset(static::$slugableCache[$string])) {
            static::$slugableCache[$string] = static::lower(static::realWords($string));
        }

        return str_replace(' ', $separator, static::$slugableCache[$string]);
    }

    private static function transliterateToEnglish(string $string, string $lang = 'en'): string
    {
        if ($lang === 'en' && extension_loaded('intl')) {
            return transliterator_transliterate('Any-Latin; Latin-ASCII;', $string);
        }

        return $string;
    }

    private static function protectCompoundWords(string $string): array
    {
        if (static::$compoundWords === []) {
            return [$string, []];
        }

        $placeholderMap = [];

        foreach (static::$compoundWords as $index => $compoundWord) {
            $placeholder = 'reservedcompoundwordplaceholder' . sprintf('%04d', $index);
            $string = preg_replace('/' . preg_quote($compoundWord, '/') . '/i', $placeholder, $string);
            $placeholderMap[$placeholder] = $compoundWord;
        }

        return [$string, $placeholderMap];
    }

    private static function restoreCompoundWords(string $string, array $placeholderMap): string
    {
        if ($placeholderMap === []) {
            return $string;
        }

        return str_replace(array_keys($placeholderMap), array_values($placeholderMap), $string);
    }
}
