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

use InvalidArgumentException;
use Webshr\Core\Assets\Contracts\Manifest as Manifest_Interface;
use Webshr\Core\Assets\Exceptions\Manifest_Not_Found_Exception;

/**
 * Manage assets manifests
 *
 * @see \Illuminate\Support\Manager
 * @link https://github.com/illuminate/support/blob/8.x/Manager.php
 */
class Manager
{
    /**
     * Resolved manifests
     *
     * @var Manifest_Interface[]
     */
    protected $manifests;
    /**
     * Assets Config
     *
     * @var array
     */
    protected $config;
    /**
     * Initialize the Asset Manager instance.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Register the given manifest
     *
     * @param  Manifest  $manifest
     * @return static
     */
    public function register(string $name, Manifest_Interface $manifest): self
    {
        $this->manifests[$name] = $manifest;
        return $this;
    }

    /**
     * Get a Manifest
     */
    public function manifest(string $name, ?array $config = null): Manifest_Interface
    {
        $manifest = $this->manifests[$name] ?? $this->resolve($name, $config);
        return $this->manifests[$name] = $manifest;
    }

    /**
     * Resolve the given manifest.
     *
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name, ?array $config): Manifest_Interface
    {
        $config = $config ?? $this->get_config($name);
        if (isset($config['handler'])) {
            return new $config['handler']($config);
        }

        $path    = $config['path'];
        $url     = $config['url'];
        $assets  = isset($config['assets']) ? $this->get_json_manifest($config['assets']) : [];
        $bundles = isset($config['bundles']) ? $this->get_json_manifest($config['bundles']) : [];
        return new Manifest($path, $url, $assets, $bundles);
    }


    /**
     * Opens a JSON manifest file from the local file system
     *
     * @param  string  $json_manifest  Path to .json file
     */
    protected function get_json_manifest(string $json_manifest): array
    {
        if (! file_exists($json_manifest)) {
            throw new Manifest_Not_Found_Exception("The asset manifest [{$json_manifest}] cannot be found.");
        }

        return json_decode(file_get_contents($json_manifest), true) ?? [];
    }

    /**
     * Get the assets manifest configuration.
     */
    protected function get_config(string $name): array
    {
        return $this->config['manifests'][$name];
    }
}
