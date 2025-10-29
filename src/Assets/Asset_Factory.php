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
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/
 */

namespace Webshr\Core\Assets;

use Webshr\Core\Assets\Asset\Asset;
use Webshr\Core\Assets\Asset\Json_Asset;
use Webshr\Core\Assets\Asset\PHP_Asset;
use Webshr\Core\Assets\Asset\Meta_Asset;
use Webshr\Core\Assets\Asset\Svg_Asset;
use Webshr\Core\Assets\Contracts\Asset as Asset_Interface;

class Asset_Factory
{
    /**
     * Create Asset instance.
     *
     * @param  string  $path  Local path
     * @param  string  $uri  Remote URI
     * @param  string  $type  Asset type
     */
    public static function create(string $path, string $uri, ?string $type = null): Asset_Interface
    {
        if (! $type) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
        }

        if ($type === 'meta') {
            $path .= '.asset.php';
        }

        $method = 'create_' . strtolower($type) . '_asset';
        if (method_exists(self::class, $method)) {
            return self::{$method}($path, $uri);
        }

        return self::create_asset($path, $uri);
    }

    /**
     * Convert an asset to another asset type.
     */
    public static function convert(Asset_Interface $asset, string $type): Asset_Interface
    {
        return self::create($asset->path(), $asset->uri(), $type);
    }

    /**
     * Create Asset instance.
     */
    protected static function create_asset(string $path, string $uri): Asset
    {
        return new Asset($path, $uri);
    }

    /**
     * Create Json_Asset instance.
     */
    protected static function create_json_asset(string $path, string $uri): Json_Asset
    {
        return new Json_Asset($path, $uri);
    }

    /**
     * Create PHP_Asset instance.
     */
    protected static function create_php_asset(string $path, string $uri): PHP_Asset
    {
        return new PHP_Asset($path, $uri);
    }

    /**
     * Create Meta_Asset instance.
     */
    protected static function create_meta_asset(string $path, string $uri): Meta_Asset
    {
        return new Meta_Asset($path, $uri);
    }

    /**
     * Create Svg_Asset instance.
     */
    protected static function create_svg_asset(string $path, string $uri): Svg_Asset
    {
        return new Svg_Asset($path, $uri);
    }
}
