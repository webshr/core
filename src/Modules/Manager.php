<?php

/**
 * @package   Webshr\Theme
 * @since     1.0.0
 * @version   1.0.0
 * @author     Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Modules;

use InvalidArgumentException;
use Webshr\Core\Modules\Contracts\Module as Module_Interface;

/**
 * Theme module manager.
 * Provides a set of methods to manage theme modules.
 */
class Manager
{
    /**
     * Resolved modules.
     *
     * @var Module_Interface[]
     */
    protected $modules = [];
    /**
     * Assets Config
     *
     * @var array
     */
    protected $config;
    /**
     * Constructor.
     *
     * @param array $modules Associative array of modules.
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Register the given module
     *
     * @param  Module_Interface $module
     * @return static
     */
    public function register(string $name, Module_Interface $module): self
    {
        $this->modules[$name] = $module;
        return $this;
    }

    /**
     * Get a Module
     *
     */
    public function module(string $name, ?array $config = null): Module_Interface
    {
        $module = $this->modules[$name] ?? $this->resolve($name, $config);
        return $this->modules[$name] = $module;
    }

    /**
     * Get all modules.
     */
    public function modules(): array
    {
        return $this->modules;
    }

    /**
     * Resolve the given module.
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name, ?array $config): Module_Interface
    {
        $config = $config ?? $this->get_config($name);

        if (isset($config['handler'])) {
            return new $config['handler']($config);
        }

        // If there's no handler in the config, use the module class directly from the modules array
        if (isset($this->config['modules'][$name])) {
            $moduleClass = $this->config['modules'][$name];
            return new $moduleClass($this->app ?? null);
        }

        throw new InvalidArgumentException("Module '{$name}' has no handler defined and could not be resolved.");
    }

    /**
     * Get the modules configuration.
     */
    protected function get_config(string $name): array
    {
        return $this->config['modules'][$name];
    }
}
