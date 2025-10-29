<?php

/**
 * @package   Webshr\Theme
 * @since     1.0.0
 * @version   1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Modules\Contracts;

/**
 * Deferrable module interface.
 */
interface Deferrable_Module
{
    /**
     * Get the services provided by the module.
     *
     * @return array
     */
    public function provides();
}
