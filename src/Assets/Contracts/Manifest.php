<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file was copied from Roots\Acorn.
 * Original author: Roots
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/Assets/Contracts/Manifest.php
 */

namespace Webshr\Core\Assets\Contracts;

interface Manifest
{
    /**
     * Get an asset object from the Manifest
     *
     * @param  string  $key
     */
    public function asset($key): Asset;
    /**
     * Get an asset bundle from the Manifest
     *
     * @param  string  $key
     */
    public function bundle($key): Bundle;

    /**
     * Get an php file from the Manifest
     *
     * @param  string  $key
     */
    //public function php($key): Php;
}
