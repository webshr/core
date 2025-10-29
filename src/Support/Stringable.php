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

class Stringable
{
    /**
     * The underlying string value.
     *
     * @var string
     */
    protected $value;
    /**
     * Create a new instance of the class.
     *
     * @param  string  $value
     * @return void
     */
    public function __construct($value = '')
    {
        $this->value = (string) $value;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public function after($search)
    {
        return new static(Str::after($this->value, $search));
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public function after_last($search)
    {
        return new static(Str::after_last($this->value, $search));
    }

    /**
     * Trim the string of the given characters.
     *
     * @param  string  $characters
     * @return static
     */
    public function trim($characters = null)
    {
        return new static(Str::trim(...array_merge([$this->value], func_get_args())));
    }

    /**
     * Left trim the string of the given characters.
     *
     * @param  string  $characters
     * @return static
     */
    public function ltrim($characters = null)
    {
        return new static(Str::ltrim(...array_merge([$this->value], func_get_args())));
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string  $prefix
     * @return static
     */
    public function start($prefix)
    {
        return new static(Str::start($this->value, $prefix));
    }

    /**
     * Get the string value.
     *
     * @return string
     */
    public function to_string()
    {
        return $this->value;
    }

    /**
     * Get the string value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
