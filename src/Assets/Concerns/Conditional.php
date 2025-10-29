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

trait Conditional
{
    /**
     * Conditionally load assets.
     *
     * @var bool
     */
    protected $conditional = true;
    /**
     * Set conditional loading.
     *
     * @param  bool|callable  $conditional
     * @return $this
     */
    public function when($conditional, ...$args)
    {
        $this->conditional = is_callable($conditional)
            ? call_user_func($conditional, $args)
            : $conditional;
        return $this;
    }
}
