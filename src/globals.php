<?php

use Webshr\Core\Application;
use Webshr\Core\Assets\Bundle;
use Webshr\Core\Assets\Asset\Asset;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return \Webshr\Core\Contracts\Application|\Webshr\Core\Application|mixed
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Application::get_instance();
        }

        return Application::get_instance()->make($abstract, $parameters);
    }
}

if (! function_exists('lang_path')) {
    /**
     * Get the path to the language folder.
     *
     * @param  string  $path
     * @return string
     */
    function lang_path(string $path = '')
    {
        return app()->lang_path($path);
    }
}

if (! function_exists('app_public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    function app_public_path(string $path = '')
    {
        return app()->public_path($path);
    }
}

if (! function_exists('app_asset_path')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function app_asset_path(string $path, bool|null $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('asset')) {
    /**
     * Get an asset instance and return the URL or other methods.
     *
     * @param string $path
     * @param string|null $manifest
     * @return Asset
     */
    function asset(string $path, ?string $manifest = null): Asset
    {
        // Use the core `asset()` from the helpers file
        return \Webshr\Core\asset($path, $manifest);
    }
}

if (!function_exists('bundle')) {
    /**
     * Get a bundle instance and return the methods.
     *
     * @param string $path
     * @param string|null $manifest
     * @return Bundle
     */
    function bundle(string $path, ?string $manifest = null): Bundle
    {
        // Use the core `bundle()` from the helpers file
        return \Webshr\Core\bundle($path, $manifest);
    }
}

if (! function_exists('module')) {
    /**
     * Return the given module
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function module($name)
    {
        return app('modules')->module($name);
    }
}

/** Deprecated **/
if (! function_exists('asset_content')) {
    /**
     * Get the asset file content.
     *
     * @param string $path
     * @example '/icons/..svg'
     * @return void
     */

    function asset_content(string $path): void
    {
        _doing_it_wrong('asset_content', 'This method has been deprecated in favor of asset()->contents', '0.2.0');
        echo asset($path)->contents();
    }
}

if (! function_exists('asset_path')) {
    /**
     * Get the asset file path.
     *
     * @param string $path
     * @example '/images/..img'
     * @return string
     */

    function asset_path(string $path): string
    {
        _doing_it_wrong('asset_path', 'This method has been deprecated in favor of asset()->path', '0.2.0');
        return asset($path)->path();
    }
}

if (! function_exists('asset_uri')) {
    /**
     * Get the asset file uri.
     *
     * @param string $path
     * @example '/images/..img'
     * @return string
     */

    function asset_uri(string $path): string
    {
        _doing_it_wrong('asset_uri', 'This method has been deprecated in favor of asset()->uri', '0.2.0');
        return asset($path)->uri();
    }
}
