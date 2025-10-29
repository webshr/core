<?php

namespace Webshr\Core;

use Webshr\Core\Application;
use Webshr\Core\Config\Environment;
use Webshr\Core\Assets\Bundle;
use Webshr\Core\Assets\Contracts\Asset;
use Webshr\Core\Assets\Contracts\Asset_Meta;
use Webshr\Core\Filesystem\Loader;

/**
 * Instantiate the bootloader.
 *
 * @param  Application|null  $app
 *
 * @return Bootloader
 */
function bootloader(?Application $app = null): Bootloader
{
    $bootloader = Bootloader::get_instance($app);

    /**
     * @deprecated
     */
    \Webshr\Core\add_actions(['after_setup_theme', 'rest_api_init'], function () use ($bootloader) {
        $app = $bootloader->get_application();

        if ($app->has_been_bootstrapped()) {
            return;
        }

        \Webshr\Core\wp_die(
            'Webshr Core failed to boot. Run <code>\\Webshr\\Core\\bootloader()->boot()</code>.<br><br>If you\'re using a Webshore Theme, you need to <a href="https://git.webshore.io/Webshr/Core/functions.php#L32">update <strong>webshr/functions.php:32</strong></a>',
            '<code>\\Webshr\\Core\\bootloader()</code> was called incorrectly.',
            'Webshr Core &rsaquo; Boot Error',
            'Check out the <a href="https://git.webshore.io/Webshr/Core">release notes</a> for more information.',
        );
    }, 6);

    return $bootloader;
}

/**
 * Get the available theme instance.
 *
 * @param  string|null  $abstract
 * @param  array  $parameters
 * @return \Webshr\Core\Application|mixed
 */
function app($abstract = null, array $parameters = [])
{
    if (is_null($abstract)) {
        return Application::get_instance();
    }
    return Application::get_instance()->make($abstract, $parameters);
}

/**
 * Require files from a directory
 *
 * @param  string|null $path
 * @return void
 */
function require_files(string $dir = null): void
{
    $loader = new Loader();
    $loader->load($dir);
}

/**
 * Get asset from manifest
 */
function asset(string $asset, ?string $manifest = null): Asset
{
    if (! $manifest) {
        return \app('assets.manifest')->asset($asset);
    }

    return \app('assets')->manifest($manifest)->asset($asset);
}

/**
 * Get bundle from manifest
 */
function bundle(string $bundle, ?string $manifest = null): Bundle
{
    if (! $manifest) {
        return \app('assets.manifest')->bundle($bundle);
    }

    return \app('assets')->manifest($manifest)->bundle($bundle);
}

/**
 * Get asset meta from manifest
 */
function meta(string $asset, ?string $manifest = null): Asset_Meta
{
    if (! $manifest) {
        return \app('assets.manifest')->meta($asset);
    } else {
        return \app('assets')->manifest($manifest)->meta($asset);
    }
}

/**
 * Get module from manager
 */
function module(string $module): Module
{
    return \app()->module_manager->module($module);
}

/**
 * Encrypts the given data.
 */
function encrypt(string $data): string
{
    return \app('encryption')->encrypt($data);
}

/**
 * Encrypts the given string without serialization.
 */
function encrypt_string(string $data): string
{
    return \app('encryption')->encrypt_string($data);
}

/**
 * Decrypts the given data.
 */
function decrypt(string $payload): string
{
    return \app('encryption')->decrypt($payload);
}

/**
 * Decrypts the given string without unserialization.
 */
function decrypt_string(string $payload): string
{
    return \app('encryption')->decrypt_string($payload);
}

/**
 * Hashes the given data.
 */
function hash(string $data): string
{
    return \app('hash')->make($data);
}

/**
 * Checks the given data against the hash.
 */
function check(string $data, string $hash): bool
{
    return \app('hash')->check($data, $hash);
}

/**
 * Gets the value of an environment variable.
 *
 * @param  string $key
 * @param  mixed  $default
 * @return mixed
 *
 * @copyright Taylor Otwell
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://github.com/laravel/framework/blob/v5.6.25/src/Illuminate/Support/helpers.php#L597-L632 Original
 *
 * @deprecated use Webshr\Core\Config\Environment::get() instead
 */
function env($key, $default = null)
{
    if (class_exists(Environment::class)) {
        return Environment::get($key, $default);
    }

    $value = getenv($key);
    if ($value === false) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return;
    }

    if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[($valueLength - 1)] === '"') {
        return substr($value, 1, -1);
    }

    return $value;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed $value
 * @return mixed
 *
 * @copyright Taylor Otwell
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://github.com/laravel/framework/blob/v5.6.25/src/Illuminate/Support/helpers.php#L1143-L1152 Original
 */
function value($value)
{
    return $value instanceof \Closure ? $value() : $value;
}

/**
 * Bind single callback to multiple filters
 *
 * @param  iterable $filters  List of filters
 * @param  callable $callback
 * @param  integer  $priority
 * @param  integer  $args
 * @return void
 */
function add_filters(iterable $filters, $callback, $priority = 10, $args = 2)
{
    $count = count($filters);
    array_map(
        '\add_filter',
        (array) $filters,
        array_fill(0, $count, $callback),
        array_fill(0, $count, $priority),
        array_fill(0, $count, $args),
    );
}

/**
 * Remove single callback from multiple filters
 *
 * @param  iterable $filters  List of filters
 * @param  callable $callback
 * @param  integer  $priority
 * @return void
 */
function remove_filters(iterable $filters, $callback, $priority = 10)
{
    $count = count($filters);
    array_map(
        '\remove_filter',
        (array) $filters,
        array_fill(0, $count, $callback),
        array_fill(0, $count, $priority),
    );
}

/**
 * Alias of add_filters
 *
 * @see add_filters
 * @param  iterable $actions  List of actions
 * @param  callable $callback
 * @param  integer  $priority
 * @param  integer  $args
 * @return void
 */
function add_actions(iterable $actions, $callback, $priority = 10, $args = 2)
{
    add_filters($actions, $callback, $priority, $args);
}

/**
 * Alias of remove_filters
 *
 * @see remove_filters
 * @param  iterable $actions  List of actions
 * @param  callable $callback
 * @param  integer  $priority
 * @return void
 */
function remove_actions(iterable $actions, $callback, $priority = 10)
{
    remove_filters($actions, $callback, $priority);
}

/**
 * Helper function for prettying up errors
 *
 * @param string $message
 * @param string $subtitle
 * @param string $title
 * @param string $footer
 */
function wp_die($message, $subtitle = '', $title = '', $footer = '')
{
    $title   = $title ?: __('WordPress &rsaquo; Error', 'webshr');
    $footer  = $footer ?: '<a href="https://webshore.eu/">Webshore</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    \wp_die($message, $title);
}
