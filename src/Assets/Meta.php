<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets;

use Webshr\Core\Assets\Contracts\Asset_Meta as Meta_Interface;

class Meta implements Meta_Interface
{
    //use Conditional, Enqueuable;

    /**
     * The meta ID.
     *
     * @var string
     */
    protected $id;
    /**
     * The meta path.
     *
     * @var string
     */
    protected $path;
    /**
     * The meta URI.
     *
     * @var string
     */
    protected $uri;
    /**
     * The meta contents.
     *
     * @var array
     */
    protected $meta;
    /**
     * Create a new meta.
     */
    public function __construct(string $id, array $meta, string $path, string $uri = '/')
    {
        $this->id   = $id;
        $this->path = $path;
        $this->uri  = $uri;
        $this->meta = $meta;
        $this->dependencies();
        $this->version();
    }

    /**
     * Get the meta dependencies.
     *
     * @return array
     */
    public function version()
    {
        return $this->meta['version'] ?? '';
    }

    /**
     * Get the meta dependencies.
     *
     * @return array
     */
    public function dependencies()
    {
        return $this->meta['dependencies'] ?? [];
    }
}
