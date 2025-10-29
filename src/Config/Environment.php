<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file contains a customized adaptation of the Laravel\Illuminate package.
 * Original author: Laravel
 * Original source: https://laravel.com/api/master/Illuminate/Foundation.html
 */

namespace Webshr\Core\Config;

use RuntimeException;

class Environment
{
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return \Webshr\Core\value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[($valueLength - 1)] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Get the value of a required environment variable.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function get_or_fail($key)
    {
        $value = static::get($key);

        if (is_null($value)) {
            throw new RuntimeException("Environment variable [{$key}] has no value.");
        }

        return $value;
    }

    /**
     * Check if an environment variable exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        return getenv($key) !== false;
    }

    /**
     * Set an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public static function set($key, $value)
    {
        return putenv("{$key}={$value}");
    }

    /**
     * Get all environment variables.
     *
     * @return array
     */
    public static function all()
    {
        return getenv();
    }
}
