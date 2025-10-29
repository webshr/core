<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Contracts;

interface Php
{
    /**
     * Get an php file from the Manifest
     *
     * @param  string  $key
     */
    public function php();
}
