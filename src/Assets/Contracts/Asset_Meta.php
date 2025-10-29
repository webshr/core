<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Contracts;

interface Asset_Meta
{
    /**
     * Get the asset version.
     *
     * @return string
     */
    public function version();
    /**
     * Get the asset dependencies.
     *
     * @return array
     */
    public function dependencies();
}
