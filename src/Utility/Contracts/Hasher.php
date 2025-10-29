<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 */

namespace Webshr\Core\Utility\Contracts;

interface Hasher
{
    /**
     * Create a hash for the given password.
     *
     * @param string $password The password to be hashed.
     * @return string The hashed password.
     */
    public function make($password);
    /**
     * Verifies that the given password matches the provided hash.
     *
     * @param string $password The plain text password to check.
     * @param string $hash The hashed password to compare against.
     * @return bool True if the password matches the hash, false otherwise.
     */
    public function check($password, $hash);
}
