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

namespace Webshr\Core\Assets\Concerns;

use Webshr\Core\Support\Str;
use Webshr\Core\Filesystem\Filesystem;

trait Enqueuable
{
    /**
     * Resolved inline sources.
     *
     * @var array
     */
    protected static $inlined = [];
    /**
     * Get JS files in bundle.
     *
     * Optionally pass a function to execute on each JS file.
     *
     * @return array|$this
     */
    abstract public function js(?callable $callable = null);
    /**
     * Get CSS files in bundle.
     *
     * Optionally pass a function to execute on each CSS file.
     *
     * @return array|$this
     */
    abstract public function css(?callable $callable = null);
    abstract public function runtime();
    abstract public function runtime_source();
    /**
     * Enqueue CSS files in WordPress.
     *
     * @return $this
     */
    public function enqueue_css(string $media = 'all', array $dependencies = [])
    {
        $this->css(function ($handle, $src) use (&$dependencies, $media) {

            wp_enqueue_style($handle, $src, $dependencies, null, $media);
            $this->merge_dependencies($dependencies, [$handle]);
        });
        return $this;
    }

    /**
     * Enqueue JS files in WordPress.
     *
     * @return $this
     */
    public function enqueue_js(bool|array $args = true, array $dependencies = [])
    {
        $this->js(function ($handle, $src, $bundleDependencies) use (&$dependencies, $args) {

            $this->merge_dependencies($dependencies, $bundleDependencies);
            wp_enqueue_script($handle, $src, $dependencies, null, $args);
            $this->inline_runtime();
            $this->merge_dependencies($dependencies, [$handle]);
        });
        return $this;
    }

    /**
     * Enqueue JS and CSS files in WordPress.
     *
     * @return $this
     */
    public function enqueue()
    {
        return $this->enqueue_css()->enqueue_js();
    }

    /**
     * Add CSS files as editor styles in WordPress.
     *
     * @return $this
     */
    public function editor_styles()
    {
        $relative_path = (new Filesystem())->get_relative_path(Str::finish(get_theme_file_path(), '/'), $this->path);
        $this->css(function ($handle, $src) use ($relative_path) {

            if (! Str::starts_with($src, $this->uri)) {
                return add_editor_style($src);
            }

            $style = Str::of($src)
                ->after($this->uri)
                ->ltrim('/')
                ->start("{$relative_path}/")
                ->to_string();
            add_editor_style($style);
        });
        return $this;
    }

    /**
     * Dequeue CSS files in WordPress.
     *
     * @return $this
     */
    public function dequeue_css()
    {
        $this->css(function ($handle) {

            wp_dequeue_style($handle);
        });
        return $this;
    }

    /**
     * Dequeue JS files in WordPress.
     *
     * @return $this
     */
    public function dequeue_js()
    {
        $this->js(function ($handle) {

            wp_dequeue_script($handle);
        });
        return $this;
    }

    /**
     * Dequeue JS and CSS files in WordPress.
     *
     * @return $this
     */
    public function dequeue()
    {
        return $this->dequeue_css()->dequeue_js();
    }

    /**
     * Inline runtime.js in WordPress.
     *
     * @return $this
     */
    public function inline_runtime()
    {
        if (! $runtime = $this->runtime()) {
            return $this;
        }

        if (isset(self::$inlined[$runtime])) {
            return $this;
        }

        if ($contents = $this->runtime_source()) {
            $this->inline($contents, 'before');
        }

        self::$inlined[$runtime] = $contents;
        return $this;
    }

    /**
     * Add an inline script before or after the bundle loads
     *
     * @param  string  $contents
     * @param  string  $position
     * @return $this
     */
    public function inline($contents, $position = 'after')
    {
        if (! $handles = array_keys($this->js()->keys()->toArray())) {
            return $this;
        }

        $handle = "{$this->id}/" . (
            $position === 'after'
            ? array_pop($handles)
            : array_shift($handles)
        );
        wp_add_inline_script($handle, $contents, $position);
        return $this;
    }

    /**
     * Add localization data to be used by the bundle
     *
     * @param  string  $name
     * @param  array  $object
     * @return $this
     */
    public function localize($name, $object)
    {
        if (! $handles = $this->js()->keys()->toArray()) {
            return $this;
        }

        $handle = "{$this->id}/{$handles[0]}";
        wp_localize_script($handle, $name, $object);
        return $this;
    }

    /**
     * Add script translations to be used by the bundle
     *
     * @param  string  $domain
     * @param  string  $path
     * @return $this
     */
    public function translate($domain = null, $path = null)
    {
        $domain ??= wp_get_theme()->get('TextDomain');
        $path ??= lang_path();
        $this->js()->keys()->each(function ($handle) use ($domain, $path) {

            wp_set_script_translations("{$this->id}/{$handle}", $domain, $path);
        });
        return $this;
    }

    /**
     * Merge two or more arrays.
     *
     * @return void
     */
    protected function merge_dependencies(array &$dependencies, array ...$moreDependencies)
    {
        $dependencies = array_unique(array_merge($dependencies, ...$moreDependencies));
    }

    /**
     * Reset inlined sources.
     *
     * @internal
     *
     * @return void
     */
    public static function reset_inlined_sources()
    {
        self::$inlined = [];
    }
}
