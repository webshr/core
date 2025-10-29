<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Asset;

use Webshr\Core\Assets\Contracts\Asset_Meta;

class Meta_Asset extends Php_Asset implements Asset_Meta
{
    /**
     * The asset content
     */
    protected $contents;
    /**
     * The asset version
     *
     * @var string
     */
    protected $version;
    /**
     * The asset dependencies
     *
     * @var array
     */
    protected $dependencies;
    /**
     * Constructor to initialize the asset.
     *
     * @param string $path
     * @param string $uri
     */
    public function __construct(string $path, string $uri)
    {
        parent::__construct($path, $uri);
        $this->contents = $this->include_once();
        $this->version = $this->contents['version'] ?? '';
        $this->dependencies = $this->contents['dependencies'] ?? [];
    }

    /**
     * Get the asset version.
     *
     * @return string
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * Get the asset dependencies.
     *
     * @return string
     */
    public function dependencies(): string
    {
        return json_encode($this->dependencies);
    }
}
