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

use Webshr\Core\Utility\Exceptions\Decrypt_Exception;
use Webshr\Core\Utility\Exceptions\Encrypt_Exception;

interface String_Encrypter
{
    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws Encrypt_Exception
     */
    public function encrypt_string(string $value);
    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws Decrypt_Exception
     */
    public function decrypt_string(string $payload);
}
