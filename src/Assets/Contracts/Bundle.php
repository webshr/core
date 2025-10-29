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
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/Assets/Contracts/Bundle.php
 */

namespace Webshr\Core\Assets\Contracts;

interface Bundle
{
    public function css();
    public function js();
    public function runtime();
}
