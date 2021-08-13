<?php

namespace CodeArtery\String;

class StrTo
{
    protected static array $real_words = [];
    protected static array $snake_cache = [];

    /**
     * Convert string to `TitleCase`
     */
    public static function title(string $string): string
    {
        return mb_convert_case(static::realWords($string), MB_CASE_TITLE_SIMPLE, 'UTF-8');
    }

    /**
     * Smart version of ucwords()
     * "DB settings" => "DB Settings" (Not "Db Settings")
     */
    public static function words(string $string) : string
    {
        $parts = explode(' ', static::realWords($string));

        $uc_words = array_map(fn ($value) => static::ucfirst($value), $parts);

        return implode(' ', $uc_words);
    }

    /**
     * Convert string to snake case
     */
    public static function snake(string $string) : string
    {
        if (isset(static::$snake_cache[$string])) {
            return static::$snake_cache[$string];
        }

        if (ctype_lower($string)) {
            return $string;
        }
    
        return static::$snake_cache[$string] = str_replace(' ', '_', static::lower(static::realWords($string)));
    }

    public static function kebab(string $string) : string
    {
        return str_replace('_', '-', static::snake($string));
    }

    /**
     * Convert string to StudlyCase
     */
    public static function studly(string $string) : string
    {
        return str_replace(' ', '', static::title($string));
    }

    /**
     * Convert string to camelCase
     */
    public static function camel(string $string) : string
    {
        return static::lcfirst(static::studly($string));
    }

    public static function dotted(string $string) : string
    {
        return str_replace('_', '.', static::snake($string));
    }

    /**
     * Convert the given string to upper case.
     */
    public static function upper(string $string): string
    {
        return mb_convert_case($string, MB_CASE_UPPER_SIMPLE, "UTF-8");
    }

    /**
     * Convert the given string to lower case.
     */
    public static function lower(string $string): string
    {
        return mb_convert_case($string, MB_CASE_LOWER_SIMPLE, "UTF-8");
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

        // Replace all characters (except alphabets, digits and underscores) with space
        $string = preg_replace('/[\W|_]+/u', ' ', $string);

        // Convert camelCaseString to space separated words, without touching UPPER CASE WORDS
        $string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);

        return static::$real_words[$key] = trim($string);
    }
}
