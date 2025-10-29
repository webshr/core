<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 */

namespace Webshr\Core\Utility;

use Webshr\Core\Utility\Contracts\Hasher as Haser_Interface;

class Hash implements Haser_Interface
{
    /**
     * {@inheritDoc}
     */
    public function make($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * {@inheritDoc}
     */
    public function check($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
