<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core;

use Webshr\Core\Modules\Contracts\Module as Module_Interface;
use Webshr\Core\Modules\Module\Module as Base_Module;

/**
 * Module class.
 */
class Module extends Base_Module implements Module_Interface
{
    /**
     * Determine if the module can be registered.
     *
     * @return bool
     */
    public function can_register(): bool
    {
        return true;
    }

    /**
     * Register the module in WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Register the action hooks for the module.
     *
     * @return void
     */
    public function register_actions(): void
    {
        // Default implementation can be overridden by child classes
    }

    /**
     * Register the filter hooks for the module.
     *
     * @return void
     */
    public function register_filters(): void
    {
        // Default implementation can be overridden by child classes
    }
}
