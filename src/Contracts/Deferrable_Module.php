<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file contains a customized adaptation of the Illuminate\Foundation package.
 * Original author: Laravel
 * Original source: https://laravel.com/api/master/Illuminate/Foundation.html
 */

namespace Webshr\Core\Contracts;

interface Deferrable_Module
{
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides();
}
