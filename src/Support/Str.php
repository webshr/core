<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file contains a customized adaptation of the Laravel\Illuminate\Foundation package.
 * Original author: Laravel
 * Original source: https://laravel.com/api/master/Illuminate/Foundation.html
 */

namespace Webshr\Core\Support;

use RuntimeException;

class Str
{
    private const TRIM_UNICODE_CLASS = '\s\x{FEFF}\x{200B}\x{200E}';

    /**
     * Check if a given substring is present within a subject string.
     *
     * @param string $string The string to search within.
     * @param string $substring The substring to search for.
     * @return bool True if the substring is found within the subject, false otherwise.
     */
    public static function contains(string $string, string $substring): bool
    {
        if ('' === $substring) {
            return false;
        }

        if ('' === $string) {
            return false;
        }

        return false !== mb_strpos($string, $substring);
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param  string  $string
     * @param  string  $substring
     * @return string
     */
    public static function before(string $string, string $substring): bool|string
    {
        if ($substring === '') {
            return $string;
        }

        $result = strstr($string, (string) $substring, true);
        return $result === false ? $string : $result;
    }

    /**
     * Check if the given string contains any of the specified substrings.
     *
     * @param string $string The string to search within.
     * @param array $substrings An array of substrings to search for.
     * @return bool True if any of the substrings are found in the subject string, false otherwise.
     */
    public static function contains_any(string $string, array $substrings): bool
    {
        foreach ($substrings as $substring) {
            if (self::contains($string, $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $prefix
     * @return string
     */
    public static function start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $value);
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $string
     * @param  string|iterable<string>  $substrings
     * @return bool
     */
    public static function starts_with(string $string, string $substrings): bool
    {
        if (! is_iterable($substrings)) {
            $substrings = [$substrings];
        }

        foreach ($substrings as $substring) {
            if ((string) $substring !== '' && str_starts_with($string, $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $string The string to search in.
     * @param string $substring The substring to search for.
     * @return bool True if the subject ends with the substring, false otherwise.
     */
    public static function ends_with(string $string, string $substring): bool
    {
        if ('' === $substring) {
            return false;
        }

        return substr($string, -strlen($substring)) === $substring;
    }

    /**
     * Determine if a given string does not end with a given substring.
     *
     * @param string $string The string to check.
     * @param string $substring The substring to search for at the end of the subject string.
     * @return bool True if the subject string does not end with the given substring, false otherwise.
     */
    public static function does_not_end_with(string $string, string $substring): bool
    {
        return ! self::ends_with($string, $substring);
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function after(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Returns the portion of the string after the first occurrence of the given search string.
     *
     * @param string $string The input string.
     * @param string $substring The string to search for.
     * @return string The portion of the string after the first occurrence of the search string.
     */
    public static function after_first(string $string, string $substring): string
    {
        return '' === $substring ? $string : array_reverse(explode($substring, $string, 2))[0];
    }

    /**
     * Returns the portion of the string after the last occurrence of a given substring.
     *
     * @param string $string The string to search in.
     * @param string $substring The substring to search for.
     * @return string The portion of the string after the last occurrence of the substring.
     * @throws RuntimeException If the substr function returns false.
     */
    public static function after_last(string $string, string $substring): string
    {
        if ('' === $substring) {
            return $string;
        }

        $position = strrpos($string, $substring);
        if (false === $position) {
            return $string;
        }

        $res = substr($string, $position + strlen($substring));
        if (false === $res) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(sprintf('substr returned false for subject [%s].', $string));
            // @codeCoverageIgnoreEnd
        }

        return $res;
    }

    /**
     * Extracts the substring between the first occurrence of the given "from" and "to" strings.
     *
     * Usage: Str::between_first('xayyy', 'x', 'y') => 'a'.
     *
     * @param string $string The string to extract the substring from.
     * @param string $from The starting delimiter string.
     * @param string $to The ending delimiter string.
     * @return string The extracted substring. If either "from" or "to" is an empty string,
     *                the original subject is returned.
     */
    public static function between_first(string $string, string $from, string $to): string
    {
        if ('' === $from) {
            return $string;
        }

        if ('' === $to) {
            return $string;
        }

        return self::before_first(self::after_first($string, $from), $to);
    }

    /**
     * Extracts the substring between the last occurrence of the given "from" and "to" strings.
     *
     * Usage: Str::between_last('xayyy', 'x', 'y') => 'a'.
     *
     * @param string $string The input string from which to extract the substring.
     * @param string $from The starting delimiter string.
     * @param string $to The ending delimiter string.
     * @return string The extracted substring, or the entire `$string` string if
     *                either `$from` or `$to` is empty.
     */
    public static function between_last(string $string, string $from, string $to): string
    {
        if ('' === $from) {
            return $string;
        }

        if ('' === $to) {
            return $string;
        }

        return self::before_last(self::after_first($string, $from), $to);
    }

    /**
     * Returns the portion of the string before the first occurrence of the given search string.
     *
     * @param string $string The input string to search within.
     * @param string $substring The string to search for.
     * @return string The portion of the input string before the first occurrence of the search string,
     *                or the entire input string if the search string is not found or is empty.
     */
    public static function before_first(string $string, string $substring): string
    {
        if ('' === $substring) {
            return $string;
        }

        $result = strstr($string, $substring, true);
        return false === $result ? $string : $result;
    }

    /**
     * Returns the portion of the string before the last occurrence of the given substring.
     *
     * @param string $string The input string.
     * @param string $substring The substring to search for.
     * @return string The portion of the string before the last occurrence of the substring.
     */
    public static function before_last(string $string, string $substring): string
    {
        if ('' === $substring) {
            return $string;
        }

        $pos = mb_strrpos($string, $substring);
        if (false === $pos) {
            return $string;
        }

        return self::substr($string, 0, $pos);
    }

    /**
     * Returns the portion of the string specified by the start and length parameters.
     *
     * @param string $string The input string.
     * @param int $start The starting position.
     * @param int|null $length The length of the substring. If omitted, the substring will extend
     *                         to the end of the string.
     * @return string The extracted substring.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    public static function finish(string $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');
        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }

    /**
     * Get a new stringable object from the given string.
     *
     * @param  string  $string
     * @return \Webshr\Core\Support\Stringable
     */
    public static function of(string $string): Stringable
    {
        return new Stringable($string);
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * This method checks if the given subject string matches the specified pattern.
     * If the pattern is an exact match to the subject, it returns true immediately.
     * Otherwise, it translates asterisks in the pattern into regular expression
     * wildcards and performs a pattern match against the subject string.
     *
     * @param string $string The string to be checked.
     * @param string $pattern The pattern to match against the subject.
     * @return bool True if the subject matches the pattern, false otherwise.
     */
    public static function is(string $string, string $pattern): bool
    {
        // If the given value is an exact match we can of course return true right
        // from the beginning. Otherwise, we will translate asterisks and do an
        // actual pattern match against the two strings to see if they match.
        if ($pattern === $string) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');
        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);
        return 1 === preg_match('#^' . $pattern . '\z#u', $string);
    }

    /**
     * Replace all occurrences of a substring within a string with another string.
     *
     * @param string $string The string to search and replace within.
     * @param string $substring The substring to search for.
     * @param string $replace The string to replace the substring with.
     * @return string The resulting string after the replacements.
     */
    public static function replace_all(string $string, string $substring, string $replace): string
    {
        return str_replace($substring, $replace, $string);
    }

    /**
     * Remove all whitespace from both ends of a string.
     *
     * @param  string  $value
     * @param  string|null  $charlist
     * @return string
     */
    public static function trim($value, $charlist = null)
    {
        if ($charlist === null) {
            $trim_default_characters = " \n\r\t\v\0";

            $pattern = '~^[' . self::TRIM_UNICODE_CLASS . $trim_default_characters . ']+'
                . '|[' . self::TRIM_UNICODE_CLASS . $trim_default_characters . ']+$~u';

            return preg_replace($pattern, '', $value) ?? trim($value);
        }

        return trim($value, $charlist);
    }

    /**
     * Remove all whitespace from the beginning of a string.
     *
     * @param  string  $value
     * @param  string|null  $charlist
     * @return string
     */
    public static function ltrim($value, $charlist = null)
    {
        if ($charlist === null) {
            $trim_default_characters = " \n\r\t\v\0";

            $pattern = '~^[' . self::TRIM_UNICODE_CLASS . $trim_default_characters . ']+~u';

            return preg_replace($pattern, '', $value) ?? ltrim($value);
        }

        return ltrim($value, $charlist);
    }
}
