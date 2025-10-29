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
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/Assets/Contracts/Asset.php
 */

namespace Webshr\Core\Assets\Contracts;

interface Asset
{
    /**
     * Get the asset's remote URI
     *
     * Example: https://example.com/app/themes/sage/dist/styles/a1b2c3.min.css
     */
    public function uri(): string;
    /**
     * Get the asset's local path
     *
     * Example: /srv/www/example.com/current/web/app/themes/sage/dist/styles/a1b2c3.min.css
     */
    public function path(): string;
    /**
     * Check whether the asset exists on the file system
     */
    public function exists(): bool;
    /**
     * Get the contents of the asset
     *
     * @return mixed
     */
    public function contents();
    /**
     * Get the relative path to the asset.
     *
     * @param  string  $base_path  Base path to use for relative path.
     */
    public function relative_path(string $base_path): string;
    /**
     * Get data URL of asset.
     *
     * @return string
     */
    public function data_url();
    /**
     * Get asset file.
     *
     * @return string
     */
    public function file();
    /**
     * Include asset.
     *
     * @return mixed
     */
    public function include();

    /**
     * Get raw content of asset.
     *
     * @return string
     */
    //public function php ();
}
