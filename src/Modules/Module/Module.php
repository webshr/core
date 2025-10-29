<?php

/**
 * @package   Webshr\Theme
 * @since     1.0.0
 * @version   1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Modules\Module;

use Closure;
use Webshr\Core\Modules\Contracts\Module as Module_Interface;
use Webshr\Core\Modules\Contracts\Deferrable_Module as Deferrable_Module_Interface;

/**
 * Theme module class.
 * Defines the basic structure for a theme module.
 *
 */
abstract class Module implements Module_Interface
{
    /**
     * The application instance.
     *
     * @var \Webshr\Core\Contracts\Application
     */
    protected $app;
    /**
     * All of the registered booting callbacks.
     *
     * @var array
     */
    protected $booting_callbacks = [];
    /**
     * All of the registered booted callbacks.
     *
     * @var array
     */
    protected $booted_callbacks = [];
    /**
     * Whether the module will be a deferred one or not.
     *
     * @var bool
     */
    protected $deferred = false;
    /**
     * Create a new module instance.
     *
     * @param  \Webshr\Core\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Checks whether the module should be registered in the current context.
     *
     * Must be implemented by the child module.
     *
     * @return bool
     */
    abstract public function can_register(): bool;
    /**
     * Register the module in WordPress.
     *
     * Must be implemented by the child module.
     *
     * @return void
     */
    abstract public function register(): void;
    /**
     * Register a booting callback to be run before the "boot" method is called.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function booting(Closure $callback)
    {
        $this->booting_callbacks[] = $callback;
    }

    /**
     * Register a booted callback to be run after the "boot" method is called.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function booted(Closure $callback)
    {
        $this->booted_callbacks[] = $callback;
    }

    /**
     * Call the registered booting callbacks.
     *
     * @return void
     */
    public function call_booting_callbacks()
    {
        $index = 0;
        while ($index < count($this->booting_callbacks)) {
            $this->app->call($this->booting_callbacks[$index]);
            $index++;
        }
    }

    /**
     * Call the registered booted callbacks.
     *
     * @return void
     */
    public function call_booted_callbacks()
    {
        $index = 0;
        while ($index < count($this->booted_callbacks)) {
            $this->app->call($this->booted_callbacks[$index]);
            $index++;
        }
    }

    /**
     * Get the services provided by the module.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this module to register.
     *
     * @return array
     */
    public function when()
    {
        return [];
    }

    /**
     * Determine if the module is deferred.
     *
     * @return bool
     */
    public function is_deferred()
    {
        return $this instanceof Deferrable_Module_Interface;
    }
}
