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
 * Theme module interface.
 * Defines the contract that every module must follow.
 *
 */
interface Module
{
    /**
     * Determine whether the module can be registered in the current context.
     *
     * @return bool
     */
    public function can_register(): bool;
    /**
     * Register the module in WordPress.
     *
     * @return void
     */
    public function register(): void;
}
