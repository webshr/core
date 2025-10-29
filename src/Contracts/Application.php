<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file contains a customized adaptation of the Illuminate\Foundation package.
 * Original author: Laravel
 * Original source: https://laravel.com/api/master/Illuminate/Foundation.html
 */

namespace Webshr\Core\Contracts;

interface Application
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version();

    /**
     * Get the base path of the installation.
     *
     * @param  string  $path
     * @return string
     */
    public function base_path($path = '');

    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path
     * @return string
     */
    public function config_path($path = '');

    /**
     * Get the path to the language files.
     *
     * @param  string  $path
     * @return string
     */
    public function lang_path($path = '');

    /**
     * Register a shared binding in the application.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * Resolve the given type from the application.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \Webshr\Core\Exceptions\Binding_Resolution_Exception
     */
    public function make($abstract, array $parameters = []);

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $default_method
     * @return mixed
     */
    public function call($callback, array $parameters = [], $default_method = null);

    /**
     * Get the value of a configuration key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key);
}
