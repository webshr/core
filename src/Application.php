<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file heavily inspired by Roots\Acorn and Illuminate\Foundation.
 * Original author: Roots/Laravel
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/
 */

namespace Webshr\Core;

use BadMethodCallException;
use Webshr\Core\Support\Arriable;
use Webshr\Core\Utility\Encryption;
use Webshr\Core\Utility\Hash;
use Webshr\Core\Assets\Manager as Assets_Manager;
use Webshr\Core\Modules\Manager as Modules_Manager;
use Webshr\Core\Support\Traits\Aliases as Aliases_Trait;
use Webshr\Core\Support\Traits\Application as App_Trait;
use Webshr\Core\Contracts\Application as Application_Interface;

use function Webshr\Core\Filesystem\join_paths;

/**
 * Main communication channel with the theme.
 */
class Application implements Application_Interface
{
    use Aliases_Trait;
    use App_Trait;

    /**
     * The core framework version.
     *
     * @var string
     */


    public const VERSION = '1.1.0';
    /**
     * The base path for the application.
     *
     * @var string
     */
    protected $base_path;
    /**
     * The custom language file path defined by the developer.
     *
     * @var string
     */
    protected $lang_path;
    /**
     * The custom environment file path defined by the developer.
     *
     * @var string
     */
    protected $env_path;
    /**
     * The custom manifests defined by the developer.
     *
     * @var array
     */
    protected $manifests;
    /**
     * The custom default manifest file defined by the developer.
     *
     * @var string
     */
    protected $default_manifest;
    /**
     * The custom language file path defined by the developer.
     *
     * @var string
     */
    protected $config_path;
    /**
     * Paths to be used by the theme.
     *
     * @var array
     */
    protected $paths = [];
    /**
     * Associative array of all core theme configs.
     *
     * @var array
     */
    protected $config = [];
    /**
     * App instance.
     *
     * @var Application
     */
    protected static $instance;
    /**
     * Assets_Manager.
     *
     * @var Assets_Manager
     */
    protected $assets_manager;
    /**
     * The Module Manager instance.
     *
     * @var Modules_Manager
     */
    public $module_manager;
    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;
    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected $has_been_bootstrapped = false;
    /**
     * The array of booting callbacks.
     *
     * @var callable[]
     */
    protected $booting_callbacks = [];
    /**
     * The array of booted callbacks.
     *
     * @var callable[]
     */
    protected $booted_callbacks = [];
    /**
     * Associative array of all core theme providers.
     *
     * @var array
     */
    protected $managers = [];
    /**
     * Associative array of all core theme modules
     *
     * @var array
     */
    protected $modules = [];
    /**
     * Associative array of all core encryption cipher and keys
     *
     * @var array
     */
    protected $encryption = [];
    /**
     * The names of the loaded modules.
     *
     * @var array
     */
    protected $registered_modules = [];
    /**
     * The names of the loaded modules.
     *
     * @var array
     */
    protected $loaded_modules = [];
    /**
     * The deferred services and their modules.
     *
     * @var array
     */
    protected $deferred_services = [];
    /**
     * The application bindings.
     *
     * @var array
     */
    protected $bindings = [];
    /**
     * Constructor to initialize the app instance.
     *
     * @param Application $instance
     */
    public function __construct($encryption = null, $base_path = null, $paths = null, $default_manifest = null, $manifests = null, $modules = null)
    {

        if ($encryption) {
            $this->use_encryption((array) $encryption);
        }

        if ($base_path) {
            $this->base_path = rtrim($base_path, '\/');
        }

        if ($paths) {
            $this->use_paths((array) $paths);
        }

        if ($default_manifest) {
            $this->use_default_manifest((string) $default_manifest);
        }

        if ($manifests) {
            $this->use_manifests((array) $manifests);
        }

        if ($modules) {
            $this->use_modules((array) $modules);
        }

        $this->register_core_bindings();
        $this->register_core_aliases();
        $this->register_core_encryption();
        $this->register_core_managers();
        $this->register_core_helpers();
    }

    /**
     * Make a new theme instance.
     *
     * @codeCoverageIgnore
     * @return static
     */
    public function make($abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    /**
     * Set the Application instance.
     *
     * @param Application $instance
     */
    public static function set_instance(Application $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * Get the globally available instance of the theme.
     *
     * @return static
     */
    public static function get_instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Boot the application's modules.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->is_booted()) {
            return;
        }

        // Once the application has booted we will also fire some "booted" callbacks
        // for any listeners that need to do work after this initial booting gets
        // finished. This is useful when ordering the boot-up processes we run.
        $this->fire_app_callbacks($this->booting_callbacks);
        // Register assets
        $this->register_assets_config();
        $this->register_modules();
        $this->boot_modules();
        $this->fire_app_callbacks($this->booted_callbacks);
        $this->booted = true;
        // Set the bootstrapped flag
        $this->has_been_bootstrapped = true;
    }

    /**
     * Set the encryption configuration.
     *
     * @param array $encryption
     * @return $this
     */
    public function use_encryption(array $encryption)
    {
        if (isset($encryption['cipher'])) {
            $this->instance('cipher', $encryption['cipher']);
        }

        if (isset($encryption['key'])) {
            $this->instance('key', $encryption['key']);
        }

        if (isset($encryption['previous_keys'])) {
            $this->instance('previous_keys', $encryption['previous_keys']);
        }

        return $this;
    }

    /**
     * Set paths that are configurable by the developer.
     *
     * Supported path types:
     * - app
     * - bootstrap
     * - config
     * - lang
     * - resources
     * - storage
     * - env
     *
     * @param  array  $path
     * @return $this
     */
    public function use_paths(array $paths)
    {
        $supported_paths = [
            'base',
            'app',
            'config',
            'resources',
            'storage',
            'bootstrap',
            'env',
        ];
        foreach ($paths as $path_type => $path) {
            $path = rtrim($path, '\\/');
            if (! in_array($path_type, $supported_paths)) {
                throw new \Exception("The {$path_type} path type is not supported.");
            }

            $this->paths[$path_type] = $path;
        }

        $this->bind_paths();
        return $this;
    }

    /**
     * Bind all of the application paths.
     *
     * @return void
     */
    protected function bind_paths()
    {
        foreach ($this->paths as $key => $path) {
            $this->instance("path.{$key}", $path);
        }

        $this->use_lang_path(is_dir($directory = $this->paths['resources'] . '/lang')
            ? $directory
            : $this->paths['base'] . '/lang');
    }

    /**
     * Set the default manifest
     *
     * @param  string  $manifest
     * @return $this
     */
    public function use_default_manifest(string $manifest)
    {
        if (! is_string($manifest)) {
            throw new \Exception("The default manifest '{$manifest}' does not exist in the manifests array.");
        }
        $this->default_manifest = $manifest;
        return $this;
    }

    /**
     * Set manifests types that are configurable by the developer.
     *
     * Supported manifest types:
     * - path
     * - url
     * - assets
     * - bundles
     *
     * @param  array  $manifests
     * @return $this
     */
    public function use_manifests(array $manifests)
    {
        $supported_manifest_keys = [
            'path',
            'url',
            'assets',
            'bundles',
        ];
        foreach ($manifests as $key => $manifest) {
            foreach ($manifest as $manifest => $value) {
                if (! in_array($manifest, $supported_manifest_keys)) {
                    throw new \Exception("The {$manifest} path type is not supported.");
                }

                $this->manifests[$key][$manifest] = rtrim($value, '\\/');
            }
        }

        return $this;
    }

    /**
     * Register assets
     *
     * @return void
     */
    protected function register_assets_config()
    {
        if (! isset($this->assets_manager)) {
            throw new \Exception("Assets manager is not initialized.");
        }

        $this->register_default_manifest();
        $this->register_manifests();
    }

    /**
     * Register the default manifest.
     *
     * @return void
     */
    protected function register_default_manifest()
    {
        if (! isset($this->default_manifest)) {
            throw new \Exception("Default manifest key '{$this->default_manifest}' is not set.");
        }

        $key      = $this->default_manifest;
        $manifest = $this->manifests[$key];
        // Check if the default manifest is already bound
        if (! isset($this->bindings['assets.manifest'])) {
            // Set the default manifest instance
            $manifest_instace = $this->assets_manager->manifest($key, $manifest);
            // Bind the Manifest instance
            $this->instance('assets.manifest', $manifest_instace);
        }
    }

    /**
     * Register all manifests.
     *
     * @return void
     */
    protected function register_manifests()
    {
        foreach ($this->manifests as $key => $manifest) {
            $this->assets_manager->manifest($key, $manifest);
        }

        $this->instance('assets', $this->assets_manager);
    }

    /**
     * Set the directory for the environment file.
     *
     * @param  string  $path
     * @return $this
     */
    public function use_environment_path($path)
    {
        $this->env_path = $path;
        $this->instance('path.env', $path);
        return $this;
    }

    /**
     * Set the language file directory.
     *
     * @param  string  $path
     * @return $this
     */
    public function use_lang_path($path)
    {
        $this->lang_path = $path;
        $this->instance('path.lang', $path);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function assets_path($path = '')
    {
        return $this->join_paths($this->config_path, $path);
    }

    /**
     * @inheritDoc
     */
    public function base_path($path = '')
    {
        return $this->join_paths($this->base_path, $path);
    }

    /**
     * @inheritDoc
     */
    public function config_path($path = '')
    {
        return $this->join_paths($this->config_path, $path);
    }

    /**
     * @inheritDoc
     */
    public function lang_path($path = '')
    {
        return $this->join_paths($this->config_path, $path);
    }

    /**
     * @inheritDoc
     */
    public function manifest_path($path = '')
    {
        return $this->join_paths($this->config_path, $path);
    }


    /**
     * Join the given paths together.
     *
     * @param  string  $base_path
     * @param  string  $path
     * @return string
     */
    public function join_paths($base_path, $path = '')
    {
        return join_paths($base_path, $path);
    }

    protected function register_core_encryption()
    {
        $this->instance('hash', new Hash());
        $this->instance('encryption', new Encryption($this));
    }

    /**
     * Load global helper functions.
     *
     * @return void
     */
    protected function register_core_helpers()
    {
        require_once __DIR__ . '/globals.php';
        require_once __DIR__ . '/helpers.php';
    }

    /**
     * Register core bindings.
     *
     * @return void
     */
    protected function register_core_bindings()
    {
        static::set_instance($this);
        $this->instance('app', $this);
    }

    /**
     * Register core managers.
     *
     * @return void
     */
    protected function register_core_managers()
    {
        $this->assets_manager = new Assets_Manager();
        $this->module_manager = new Modules_Manager();
    }

    /**
     * Set the modules that are configurable by the developer.
     *
     * @param  array  $modules
     * @return $this
     */
    public function use_modules(array $modules)
    {
        foreach ($modules as $module_name => $module_class) {
            if (! class_exists($module_class)) {
                throw new \Exception("The module class {$module_class} does not exist.");
            }

            $this->modules[$module_name] = $module_class;
        }

        return $this;
    }

    /**
     * Register modules
     *
     * @return void
     */
    protected function register_modules()
    {
        if (! isset($this->module_manager)) {
            throw new \Exception("Modules manager is not initialized.");
        }

        foreach ($this->modules as $key => $module) {
            $this->module_manager->register($key, new $module());
            $this->registered_modules[$key] = $module;
        }
    }

    /**
     * Register a module with the application.
     *
     * @param  \Webshr\Core\Module|string  $module
     * @param  bool  $force
     */
    public function boot_modules()
    {
        foreach ($this->module_manager->modules() as $key => $module) {
            // Check if the module is already loaded
            if (isset($this->loaded_modules[$key])) {
                continue;
                // Skip already loaded modules
            }

            // Boot the module
            $this->boot_module($module);
            $this->loaded_modules[$key] = $module;
            // Register the module if it can be registered
            if ($module->can_register()) {
                $module->register();
            }

            // If the application has already booted, call the boot method on the module
            if ($this->is_booted()) {
                $this->boot_module($module);
            }
        }
    }

    /**
     * Boot the given module.
     *
     * @param  \Webshr\Core\Module  $module
     * @return void
     */
    protected function boot_module(Module $module)
    {
        $module->call_booting_callbacks();
        if (method_exists($module, 'boot')) {
            $this->call([$module, 'boot']);
        }

        $module->call_booted_callbacks();
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \Webshr\Core\Module|string  $module
     * @return \Webshr\Core\Module|null
     */
    public function get_module($module)
    {
        return array_values($this->get_modules($module))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  \Webshr\Core\Module|string  $module
     * @return array
     */
    public function get_modules($module)
    {
        $name = is_string($module) ? $module : get_class($module);
        return Arriable::where($this->modules, fn($value) => $value instanceof $name);
    }

    /**
     * Resolve a module instance from the class name.
     *
     * @param  string  $module
     * @return \Webshr\Core\Modules\Contracts\Module
     */
    public function resolve_module($module)
    {
        return $this->module_manager->module($module);
    }

    /**
     * Mark the given module as registered.
     *
     * @param  \Webshr\Core\Module  $module
     * @return void
     */
    protected function mark_as_registered($module)
    {
        $this->modules[] = $module;
        $this->loaded_modules[get_class($module)] = true;
    }

    /**
     * Register the core class aliases in the application.
     *
     * @return void
     */
    protected function register_core_aliases()
    {
        $this->alias('app', self::class);
    }

    /**
     * Register a new boot listener.
     *
     * @param  callable  $callback
     * @return void
     */
    public function booting($callback)
    {
        $this->booting_callbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  callable  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->booted_callbacks[] = $callback;
        if ($this->is_booted()) {
            $callback($this);
        }
    }

    /**
     * Check if the application has been booted.
     *
     * This method returns the status of the application's boot process.
     *
     * @return bool True if the application is booted, false otherwise.
     */
    public function is_booted()
    {
        return $this->booted;
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param  callable[]  $callbacks
     * @return void
     */
    protected function fire_app_callbacks(array &$callbacks)
    {
        $index = 0;
        while ($index < count($callbacks)) {
            $callbacks[$index]($this);
            $index++;
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function has_been_bootstrapped()
    {
        return $this->has_been_bootstrapped;
    }

    /**
     * Get an object or value from the application container.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->bindings[$key] ?? null;
    }

    /**
     * Get all bindings.
     *
     * @return array
     */
    public function bindings(): array
    {
        return $this->bindings;
    }

    /**
     * @inheritDoc
     */
    public function version()
    {
        return self::VERSION;
    }

    /**
     * Bind a value or object to a key in the application container.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function instance(string $key, $value)
    {
        $this->bindings[$key] = $value;
    }

    /**
     * Register a shared binding in the application.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        // If no concrete implementation is provided, use the abstract as the concrete
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        // Register a closure that resolves the singleton instance
        $this->bindings[$abstract] = function () use ($abstract, $concrete) {

            // Check if the instance already exists
            if (! isset($this->bindings["instances"][$abstract])) {
                // Resolve the instance and store it
                $this->bindings["instances"][$abstract] = $this->resolve($concrete);
            }

            // Return the stored instance
            return $this->bindings["instances"][$abstract];
        };
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $default_method
     * @return mixed
     */
    public function call($callback, array $parameters = [], $default_method = null)
    {
        if (is_string($callback)) {
            // Handle the case where the callback is a string in the format 'Class@method'
            if (strpos($callback, '@') !== false) {
                list($class, $method) = explode('@', $callback);
                $callback               = [new $class(), $method];
            } elseif ($default_method) {
                $callback = [new $callback(), $default_method];
            } else {
                $callback = new $callback();
            }
        }

        return call_user_func_array($callback, $parameters);
    }

    /**
     * Resolve a value or object from the application
     *
     * @param string $key The key to resolve.
     * @param array  $args The arguments to pass to the resolved service.
     * @return mixed|null The resolved service or null if not found.
     */
    public function resolve($abstract, $parameters = []): mixed
    {
        // Get the alias
        if (! is_string($abstract)) {
            return null;
        }

        // Check if the binding exists
        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            // If the module is a class name (string), instantiate it
            if (is_string($binding) && class_exists($binding)) {
                return new $binding();
                // or resolve via a DI container if needed
            }

            // If the module is a callable or has a callback, invoke it
            if (is_callable($binding)) {
                return call_user_func($binding);
            }

            if (is_array($binding) && isset($binding['callback']) && is_callable($binding['callback'])) {
                return call_user_func($binding['callback']);
            }
            // Otherwise, return the module directly
            return $binding;
        }

        return null;
        // Return null if the service is not found
    }

    /**
     * Magic call method.
     *
     * Will proxy to the theme method $method, unless it is not available, in which case an exception will be thrown.
     *
     * @param string $method Template tag name.
     * @param array  $args   Template tag arguments.
     * @return mixed Template tag result, or null if theme method only outputs markup.
     *
     * @throws BadMethodCallException Thrown if the theme method does not exist.
     */
    public function __call(string $method, array $args): mixed
    {
        $resolved = $this->resolve($method);
        if (! $resolved) {
            throw new BadMethodCallException(sprintf(__('The method %s does not exist.', 'webshr'), 'app()->' . $method . '()'),);
        }

        // If the resolved service is an invokable object, call it
        if (is_object($resolved) && is_callable($resolved)) {
            return call_user_func_array($resolved, $args);
        }

        // If the resolved service is an object (but not invokable), return the object itself
        if (is_object($resolved)) {
            return $resolved;
        }

        // Otherwise, call the resolved service as a function
        return call_user_func_array($resolved, $args);
    }
}
