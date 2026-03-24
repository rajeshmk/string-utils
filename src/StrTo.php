<?php

declare(strict_types=1);

namespace Hatchyu\String;

class StrTo
{
    protected static array $real_words = [];

    protected static array $slugable_cache = [];

    /**
     * Convert string to `TitleCase`.
     */
    public static function title(string $string): string
    {
        return mb_convert_case(static::realWords($string), MB_CASE_TITLE_SIMPLE, 'UTF-8');
    }

    /**
     * Smart version of ucwords()
     * "DB settings" => "DB Settings" (Not "Db Settings").
     */
    public static function words(string $string): string
    {
        $parts = explode(' ', static::realWords($string));

        $uc_words = array_map(fn ($value) => static::ucfirst($value), $parts);

        return implode(' ', $uc_words);
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
     * Laravel's Str::limit() won't preserve words. Use this function in such cases.
     */
    public static function slug(string $string, int $max_length = 120, string $lang = 'en'): string
    {
        // Replace @ with the word 'at'
        $string = str_replace('@', '-at-', $string);

        $string = static::transliterateToEnglish($string, $lang);

        // Keep lower case words separated by SPACE itself
        $string = static::slugable($string, ' ');

        // Keep `$max_length` - Wordwrap separated by '@'
        $wrapped_text = wordwrap($string, $max_length, '@', true);
        $string = str_replace(' ', '-', current(explode('@', $wrapped_text)));

        // Remove non-alphanumeric characters
        if ($lang === 'en') {
            $string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);
        }

        return $string;
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

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private static function realWords(string $string): string
    {
        $key = $string;

        if (isset(static::$real_words[$key])) {
            return static::$real_words[$key];
        }

        // Replace all characters (except letters, numbers and underscores) with space
        $string = preg_replace('/[\W|_]+/u', ' ', $string);
        $string = preg_replace('/([A-Z]{2,})([A-Z][a-z])/', '$1 $2', $string);
        $string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
        $string = preg_replace('/(\d)([A-Za-z])/', '$1 $2', $string);

        return static::$real_words[$key] = trim(preg_replace('/\s+/', ' ', $string));
    }

    private static function slugable(string $string, string $separator = '-'): string
    {
        if (ctype_lower($string)) {
            return $string;
        }

        if (! isset(static::$slugable_cache[$string])) {
            static::$slugable_cache[$string] = static::lower(static::realWords($string));
        }

        return str_replace(' ', $separator, static::$slugable_cache[$string]);
    }

    private static function transliterateToEnglish(string $string, string $lang = 'en'): string
    {
        if ($lang === 'en' && extension_loaded('intl')) {
            return transliterator_transliterate('Any-Latin; Latin-ASCII;', $string);
        }

        return $string;
    }
}
