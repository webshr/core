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

namespace Webshr\Core\Support\Contracts;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function to_json();
}
