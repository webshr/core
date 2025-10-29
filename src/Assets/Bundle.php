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

use Webshr\Core\Support\Arriable;
use Webshr\Core\Assets\Concerns\Conditional;
use Webshr\Core\Assets\Concerns\Enqueuable;
use Webshr\Core\Assets\Contracts\Bundle as Bundle_Interface;

class Bundle implements Bundle_Interface
{
    use Conditional;
    use Enqueuable;

    /**
     * The bundle ID.
     *
     * @var string
     */


    protected $id;
    /**
     * The bundle path.
     *
     * @var string
     */
    protected $path;
    /**
     * The bundle URI.
     *
     * @var string
     */
    protected $uri;
    /**
     * The bundle runtime.
     *
     * @var string|null
     */
    protected $runtime;
    /**
     * The bundle contents.
     *
     * @var array
     */
    protected $bundle;
    /**
     * The bundle runtimes.
     *
     * @var array
     */
    protected static $runtimes = [];
    /**
     * Create a new bundle.
     */
    public function __construct(string $id, array $bundle, string $path, string $uri = '/')
    {
        $this->id     = $id;
        $this->path   = $path;
        $this->uri    = $uri;
        $this->bundle = $bundle + ['js' => [], 'mjs' => [], 'css' => []];
        $this->set_runtime();
    }

    /**
     * Get CSS files in bundle.
     *
     * Optionally pass a function to execute on each CSS file.
     *
     * @return array|$this
     */
    public function css(?callable $callable = null)
    {
        $styles = $this->conditional ? $this->bundle['css'] : [];
        if (! $callable) {
            return $styles;
        }

        foreach ($styles as $handle => $src) {
            $callable("{$this->id}/{$handle}", $this->get_url($src));
        }

        return $this;
    }

    /**
     * Get JS files in bundle.
     *
     * Optionally pass a function to execute on each JS file.
     *
     * @return array|$this
     */
    public function js(?callable $callable = null)
    {
        $scripts = $this->conditional ? array_merge($this->bundle['js'], $this->bundle['mjs']) : [];
        if (! $callable) {
            return $scripts;
        }

        foreach ($scripts as $handle => $src) {
            if ($handle === 'runtime') {
                continue;
            }
            $callable("{$this->id}/{$handle}", $this->get_url($src), $this->dependencies());
        }

        return $this;
    }

    /**
     * Get the bundle dependencies.
     *
     * @return array
     */
    public function dependencies()
    {
        return $this->bundle['dependencies'] ?? [];
    }

    /**
     * Get the bundle runtime.
     *
     * @return string|null
     */
    public function runtime()
    {
        return $this->runtime;
    }

    /**
     * Get bundle runtime contents.
     *
     * @return string|null
     */
    public function runtime_source()
    {
        if (($runtime = $this->runtime()) === null) {
            return null;
        }

        if ($sauce = self::$runtimes[$runtime] ?? null) {
            return $sauce;
        }

        return self::$runtimes[$runtime] = file_get_contents("{$this->path}/{$runtime}");
    }

    /**
     * Get the bundle URL.
     *
     * @return string
     */
    protected function get_url(string $path)
    {
        if (parse_url($path, PHP_URL_HOST)) {
            return $path;
        }

        $path = ltrim($path, '/');
        $uri  = rtrim($this->uri, '/');
        return "{$uri}/{$path}";
    }

    /**
     * Set the bundle runtime.
     *
     * @return void
     */
    protected function set_runtime()
    {
        if (Arriable::is_assoc($this->bundle['js'])) {
            $this->runtime = $this->bundle['js']['runtime']
                ?? $this->bundle['js']["runtime~{$this->id}"]
                ?? null;
            unset($this->bundle['js']['runtime'], $this->bundle['js']["runtime~{$this->id}"]);
            return;
        }

        $this->runtime = $this->get_bundle_runtime() ?? $this->get_bundle_runtime('mjs');
    }

    /**
     * Retrieve the runtime in a bundle.
     *
     * @return string|null
     */
    protected function get_bundle_runtime(string $type = 'js')
    {
        if (! $this->bundle[$type]) {
            return null;
        }

        foreach ($this->bundle[$type] as $key => $value) {
            if (! str_contains($value, 'runtime')) {
                continue;
            }

            unset($this->bundle[$type][$key]);
            return $value;
        }

        return null;
    }
}
