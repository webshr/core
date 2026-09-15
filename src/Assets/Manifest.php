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

use Webshr\Core\Support\Str;
use Webshr\Core\Assets\Exceptions\Bundle_Not_Found_Exception;
use Webshr\Core\Assets\Contracts\Asset as Asset_Interface;
use Webshr\Core\Assets\Contracts\Asset_Meta as Asset_Meta_Interface;
use Webshr\Core\Assets\Contracts\Bundle as Bundle_Interface;
use Webshr\Core\Assets\Contracts\Manifest as Manifest_Interface;

class Manifest implements Manifest_Interface
{
    /**
     * The manifest assets.
     *
     * @var array
     */
    protected $assets;
    /**
     * The manifest meta assets.
     *
     * @var array
     */
    protected $metas;
    /**
     * The manifest bundles.
     *
     * @var array
     */
    protected $bundles;
    /**
     * The manifest path.
     *
     * @var string
     */
    protected $path;
    /**
     * The manifest URI.
     *
     * @var string
     */
    protected $uri;
    /**
     * Create a new manifest instance.
     */
    public function __construct(
        string $path,
        string $uri,
        array $assets = [],
        ?array $bundles = null,
        ?array $metas = null
    ) {
        $this->path    = $path;
        $this->uri     = $uri;
        $this->bundles = $bundles;
        $this->metas   = $metas;
        foreach ($assets as $original => $revved) {
            $this->assets[$this->normalize_relative_path($original)] = $this->normalize_relative_path($revved);
        }
    }

    /**
     * Get specified asset.
     *
     * @param  string  $key
     */
    public function asset($key): Asset_Interface
    {
        $key          = $this->normalize_relative_path($key);
        $relativePath = $this->assets[$key] ?? $key;
        $path = Str::before("{$this->path}/{$relativePath}", '?');
        $uri  = "{$this->uri}/{$relativePath}";
        return Asset_Factory::create($path, $uri);
    }

    /**
     * Get specified asset.
     *
     * @param  string  $key
     */
    public function php($key)
    {
        $key          = $this->normalize_relative_path($key);
        $relativePath = $this->assets[$key] ?? $key;
        $path = Str::before("{$this->path}/{$relativePath}", '?');
        $uri  = "{$this->uri}/{$relativePath}";
        return Asset_Factory::create($path, $uri, 'php');
    }

    /**
     * Get specified meta asset.
     *
     * @param  string  $key
     */
    public function meta($key): Asset_Meta_Interface
    {
        $key = $this->normalize_relative_path($key);
        // Check if the meta asset is already cached
        if (isset($this->metas[$key])) {
            return $this->metas[$key];
        }

        $relativePath = $this->assets[$key] ?? $key;
        $path = Str::before("{$this->path}/{$relativePath}", '?');
        $uri  = "{$this->uri}/{$relativePath}";
        if (! isset($this->metas[$key])) {
            $meta = Asset_Factory::create($path, $uri, 'meta');
        } else {
            $meta = new Meta($key, $this->metas[$key], $this->path, $this->uri);
        }

        // Cache the created meta asset
        $this->metas[$key] = $meta;
        return $meta;
    }

    /**
     * Get specified bundles.
     *
     * @param  string  $key
     *
     * @throws \Webshr\Core\Assets\Exceptions\Bundle_Not_Found_Exception
     */
    public function bundle($key): Bundle_Interface
    {
        if (! isset($this->bundles[$key])) {
            throw new Bundle_Not_Found_Exception("Bundle [{$key}] not found in manifest.");
        }

        return new Bundle($key, $this->bundles[$key], $this->path, $this->uri);
    }

    /**
     * Normalizes to forward slashes and removes leading slash.
     */
    protected function normalize_relative_path(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('%//+%', '/', $path);
        return ltrim($path, './');
    }
}
