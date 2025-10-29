<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Asset;

use Webshr\Core\Assets\Contracts\Asset as Asset_Interface;
use Webshr\Core\Assets\Contracts\Asset_Meta as Meta_Interface;
use Webshr\Core\Filesystem\Filesystem;
use SplFileInfo;

class Asset implements Asset_Interface, Meta_Interface
{
    /**
     * The local asset path.
     *
     * @var string
     */
    protected $path;
    /**
     * The remote asset URI.
     *
     * @var string
     */
    protected $uri;
    /**
     * The asset MIME content type.
     *
     * @var string
     */
    protected $type;
    /**
     * The asset base64-encoded contents.
     *
     * @var string
     */
    protected $base64;
    /**
     * The asset data URL.
     *
     * @var string
     */
    protected $data_url;
    /**
     * Get asset from manifest
     *
     * @param  string  $path  Local path
     * @param  string  $uri  Remote URI
     */
    public function __construct(string $path, string $uri)
    {
        $this->path = $path;
        $this->uri  = $uri;
    }

    /** {@inheritdoc} */
    public function uri(): string
    {
        return $this->uri;
    }

    /** {@inheritdoc} */
    public function path(): string
    {
        return $this->path;
    }

    /** {@inheritdoc} */
    public function exists(): bool
    {
        return file_exists($this->path());
    }

    /** {@inheritdoc} */
    public function contents(): string
    {
        if (! $this->exists()) {
            return '';
        }

        return file_get_contents($this->path());
    }

    /**
     * Get the relative path to the asset.
     *
     * @param  string  $basePath  Base path to use for relative path.
     */
    public function relative_path(string $basePath): string
    {
        $basePath = rtrim($basePath, '/\\') . '/';
        return (new Filesystem())->get_relative_path($basePath, $this->path());
    }

    /**
     * Get the base64-encoded contents of the asset.
     *
     * @return string
     */
    public function base64()
    {
        if ($this->base64) {
            return $this->base64;
        }

        return $this->base64 = base64_encode($this->contents());
    }

    /**
     * Get data URL of asset.
     *
     * @param  string  $mediatype  MIME content type
     */
    public function data_url(?string $mediatype = null): string
    {
        if ($this->data_url) {
            return $this->data_url;
        }

        if (! $mediatype) {
            $mediatype = $this->content_type();
        }

        return $this->data_url = "data:{$mediatype};base64,{$this->base64()}";
    }

    /**
     * Get data URL of asset.
     *
     * @param  string  $mediatype  MIME content type
     */
    public function data_uri(?string $mediatype = null): string
    {
        return $this->data_url($mediatype);
    }

    /**
     * Get the MIME content type.
     *
     * @return string|false
     */
    public function content_type()
    {
        if ($this->type) {
            return $this->type;
        }

        return $this->type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path());
    }

    /**
     * Get the MIME content type.
     *
     * @return string|false
     */
    public function mime_type()
    {
        return $this->content_type();
    }

    /**
     * Get SplFileInfo instance of asset.
     *
     * @return SplFileInfo
     */
    public function file()
    {
        return new SplFileInfo($this->path());
    }

    /** {@inheritdoc} */
    public function raw(): mixed
    {
        if (!$this->exists()) {
            return '';
        }

        // Start output buffering
        ob_start();
        // Include the PHP file
        include $this->path();
        // Get the contents of the buffer and clean it
        $content = ob_get_clean();
        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function version()
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function dependencies()
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function include()
    {
        if (!$this->exists()) {
            return '';
        }

        return include $this->path();
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return $this->uri();
    }
}
