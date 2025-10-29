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

interface Encrypter
{
    /**
     * Encrypts the given data.
     *
     * @param string $value The data to be encrypted.
     * @param bool $serialize Whether to serialize the data.
     * @return string The encrypted data.
     * @throws Encrypt_Exception If encryption fails.
     */
    public function encrypt(string $value, bool $serialize = true): string;
    /**
     * Decrypts the given data.
     *
     * @param string $payload The data to be decrypted.
     * @param bool $unserialize Whether to unserialize the data.
     * @return mixed The decrypted data.
     * @throws Decrypt_Exception If encryption fails.
     */
    public function decrypt(string $payload, bool $unserialize = true): mixed;
    /**
     * Get the encryption key that the encrypter is currently using.
     *
     * @return string
     */
    public function get_key(): string;
    /**
     * Get the current encryption key and all previous encryption keys.
     *
     * @return array
     */
    public function get_all_keys(): array;
    /**
     * Get the previous encryption keys.
     *
     * @return array
     */
    public function get_previous_keys(): array;
}
