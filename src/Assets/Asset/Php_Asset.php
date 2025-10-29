<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Assets\Asset;

use Webshr\Core\Filesystem\Exceptions\File_Not_Found_Exception;

class Php_Asset extends Asset
{
    /**
     * Get the returned value of the asset
     *
     * @return mixed
     */
    public function require_once()
    {
        $this->assert_exists();
        return require_once $this->path();
    }

    /**
     * Get the returned value of the asset
     *
     * @return mixed
     */
    public function require()
    {
        $this->assert_exists();
        return require $this->path();
    }

    /**
     * Get the returned value of the asset
     *
     * @return mixed
     */
    public function include_once()
    {
        $this->assert_exists();
        return include_once $this->path();
    }

    /**
     * Get the returned value of the asset
     *
     * @return mixed
     */
    public function include()
    {
        $this->assert_exists();
        return include $this->path();
    }

    /**
     * Assert that the asset exists.
     *
     * @throws \Webshr\Core\Filesystem\Exceptions\File_Not_Found_Exception
     */
    protected function assert_exists()
    {
        if (! $this->exists()) {
            throw new File_Not_Found_Exception("Asset [{$this->path()}] not found.");
        }
    }
}
