<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Contracts;

interface Meta
{
    /**
     * Get an asset meta object from the Store
     *
     * @param  string  $key
     */
    public function meta($key): Asset_Meta;
}
