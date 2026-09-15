<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file heavily inspired by Roots\Acorn.
 * Original author: Roots
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/
 */

namespace Webshr\Core;

use Webshr\Core\Filesystem\Filesystem;
use Webshr\Core\Config\Repository;

class Bootloader
{
    /**
     * The Bootloader instance.
     */
    protected static $instance;
    /**
     * The Application instance.
     *
     * @var \Webshr\Core\Application|null
     */
    protected ?Application $app;
    /**
     * The Filesystem instance.
     *
     * @var \Webshr\Core\Filesystem\Filesystem
     */
    protected Filesystem $files;
    /**
     * The configuration repository.
     *
     * @var \Webshr\Core\Config\Repository
     */
    protected Repository $config;
    /**
     * The application's base path.
     *
     * @var string
     */
    protected string $base_path = '';
    /**
     * Create a new bootloader instance.
     *
     * @param  \Webshr\Core\Application|null  $app
     * @param  \Webshr\Core\Filesystem\Filesystem|null  $files
     */
    public function __construct(?Application $app = null, ?Filesystem $files = null)
    {
        $this->app   = $app;
        $this->files = $files ?? new Filesystem();
        // Load all configuration files into the repository
        $this->config = new Repository($this->load_configuration_files());
        static::$instance ??= $this;
    }

    /**
     * Boot the Application.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->boot();
    }

    /**
     * Set the Bootloader instance.
     *
     * @param  \Webshr\Core\Bootloader|null  $bootloader
     *
     * @return void
     */
    public static function set_instance(?self $bootloader): void
    {
        static::$instance = $bootloader;
    }

    /**
     * Get the Bootloader instance.
     *
     * @param  \Webshr\Core\Application|null  $app
     *
     * @return \Webshr\Core\Bootloader
     */
    public static function get_instance(?Application $app = null): static
    {
        return static::$instance ??= new static($app);
    }

    /**
     * Boot the Application.
     *
     * @param  callable|null  $callback
     *
     * @return void
     */
    public function boot(?callable $callback = null): void
    {
        $this->get_application();
        if ($callback) {
            $callback($this->app);
        }

        if ($this->app->has_been_bootstrapped()) {
            return;
        }

        $this->app->boot();
    }

    /**
     * Initialize and retrieve the Application instance.
     *
     * @return \Webshr\Core\Application
     */
    public function get_application(): Application
    {
        $this->app ??= new Application($this->encryption(), $this->base_path(), $this->use_path());
        $this->app->use_environment_path($this->environment_path());
        $this->app->use_default_manifest($this->default_manifest());
        $this->app->use_manifests($this->manifests());
        $this->app->use_modules($this->modules());
        $this->app->use_aliases($this->aliases());
        return $this->app;
    }

    /**
     * Load all configuration files.
     *
     * @return array
     */
    protected function load_configuration_files(): array
    {
        $config_path  = get_template_directory() . '/config';
        $config_files = $this->files->files($config_path);
        $config = [];
        foreach ($config_files as $file) {
            $key            = basename($file, '.php');
            $config[$key] = require $file;
        }

        return $config;
    }

    /**
     * Get the application's encryption values.
     *
     * @return array
     */
    protected function encryption(): array
    {
        $encryption                  = [];
        $encryption['cipher']        = $this->config->get('app.cipher');
        $encryption['key']           = $this->config->get('app.key');
        $encryption['previous_keys'] = $this->config->get('app.previous_keys');
        return $encryption;
    }

    /**
     * Get the application's base path.
     *
     * @return string
     */
    protected function base_path(): string
    {
        return $this->config->get('app.paths.base');
    }

    /**
     * Get the environment file path.
     *
     * @return string
     */
    protected function environment_path(): string
    {
        return $this->config->get('app.paths.env');
    }

    /**
     * Use paths that are configurable by the developer.
     *
     * @return array
     */
    protected function use_path(): array
    {
        return $this->config->get('app.paths');
    }

    /**
     * Get the default manifest name.
     *
     * @return string
     */
    protected function default_manifest(): string
    {
        return $this->config->get('assets.default');
    }

    /**
     * Get the application's manifests.
     *
     * @return array
     */
    protected function manifests(): array
    {
        return $this->config->get('assets.manifests');
    }

    /**
     * Get the application's modules
     *
     * @return array
     */
    protected function modules(): array
    {
        return $this->config->get('app.modules');
    }

    /**
     * Get the application's aliases.
     *
     * @return array
     */
    protected function aliases(): array
    {
        return $this->config->get('app.aliases') ?? [];
    }
}
