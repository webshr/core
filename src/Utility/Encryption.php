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

use RuntimeException;
use Webshr\Core\Utility\Exceptions\Encrypt_Exception;
use Webshr\Core\Utility\Exceptions\Decrypt_Exception;
use Webshr\Core\Contracts\Application as Application_Interface;
use Webshr\Core\Utility\Contracts\Encrypter as Encrypter_Interface;
use Webshr\Core\Utility\Contracts\String_Encrypter as String_Encrypter_Interface;

class Encryption implements Encrypter_Interface, String_Encrypter_Interface
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected string $key;
    /**
     * The previous / legacy encryption keys.
     *
     * @var array
     */
    protected $previous_keys = [];
    /**
     * The encryption cipher.
     *
     * @var string
     */
    protected string $cipher;
    /**
     * The supported cipher algorithms and their properties.
     *
     * @var array
     */
    private static $supported_ciphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];
    /**
     * Initialize the Encryption instance.
     *
     * @param Application_Interface $app
     */
    public function __construct(Application_Interface $app)
    {
        $key    = $app->get('key');
        $cipher = strtolower($app->get('cipher') ?? 'aes-256-cbc');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException(
                'No encryption key configured.'
                . ' Set app.key (or APP_KEY) to a random string.'
            );
        }

        if (! isset(self::$supported_ciphers[$cipher])) {
            $ciphers = implode(', ', array_keys(self::$supported_ciphers));
            throw new RuntimeException(
                "Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}."
            );
        }

        $this->cipher = $cipher;
        $this->key    = $this->derive_key($key, $cipher);
    }

    /**
     * Compatibility derivation for WordPress salts whose byte length
     * does not match the cipher; output is hex-truncated SHA-256.
     *
     * @see .claude/tasks/fix-code-review-findings.md (M1 decision)
     */
    private function derive_key(string $key, string $cipher): string
    {
        $expected_length = self::$supported_ciphers[$cipher]['size'];
        if (mb_strlen($key, '8bit') !== $expected_length) {
            $key = substr(hash('sha256', $key), 0, $expected_length);
        }
        return $key;
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $cipher = strtolower($cipher);
        if (! isset(self::$supported_ciphers[$cipher])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supported_ciphers[$cipher]['size'];
    }

    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string  $cipher
     * @return string
     */
    public static function generate_key($cipher)
    {
        return random_bytes(self::$supported_ciphers[strtolower($cipher)]['size'] ?? 32);
    }

    /**
     * {@inheritDoc}
     */
    public function encrypt(string $value, $serialize = true): string
    {
        if (! extension_loaded('openssl')) {
            throw new Encrypt_Exception('The OpenSSL extension is not loaded.');
        }

        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher,
            $this->key,
            0,
            $iv,
            $tag,
        );
        if ($value === false) {
            throw new Encrypt_Exception('Could not encrypt the data.');
        }

        $iv  = base64_encode($iv);
        $tag = base64_encode($tag ?? '');
        $mac = self::$supported_ciphers[$this->cipher]['aead']
            ? '' // For AEAD-algorithms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value, $this->key);
        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * {@inheritDoc}
     */
    public function encrypt_string(string $value): string
    {
        return $this->encrypt($value, false);
    }

    /**
     * {@inheritDoc}
     */
    public function decrypt(string $payload, bool $unserialize = true): string
    {
        $payload = $this->get_json_payload($payload);
        $iv = base64_decode($payload['iv']);
        $this->ensure_tag_is_valid($tag = empty($payload['tag']) ? null : base64_decode($payload['tag']));
        $found_valid_mac = false;
        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        foreach ($this->get_all_keys() as $key) {
            if (
                $this->should_validate_mac() &&
                ! ($found_valid_mac = $found_valid_mac || $this->valid_mac_for_key($payload, $key))
            ) {
                continue;
            }

            $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $key, 0, $iv, $tag ?? '');
            if ($decrypted !== false) {
                break;
            }
        }

        if ($this->should_validate_mac() && ! $found_valid_mac) {
            throw new Decrypt_Exception('The MAC is invalid.');
        }

        if (($decrypted ?? false) === false) {
            throw new Decrypt_Exception('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * {@inheritDoc}
     */
    public function decrypt_string(string $payload): string
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @param  string  $key
     * @return string
     */
    protected function hash(string $iv, mixed $value, string $key)
    {
        return hash_hmac('sha256', $iv . $value, $key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws Decrypt_Exception
     */
    protected function get_json_payload(string $payload): array
    {
        $payload = json_decode(base64_decode($payload), true);
        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->valid_payload($payload)) {
            throw new Decrypt_Exception('The payload is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function valid_payload(mixed $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        foreach (['iv', 'value', 'mac'] as $item) {
            if (! isset($payload[$item]) || ! is_string($payload[$item])) {
                return false;
            }
        }

        if (isset($payload['tag']) && ! is_string($payload['tag'])) {
            return false;
        }

        return strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }

    /**
     * Determine if the MAC for the given payload is valid for the primary key.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function valid_mac(array $payload): bool
    {
        return $this->valid_mac_for_key($payload, $this->key);
    }

    /**
     * Determine if the MAC is valid for the given payload and key.
     *
     * @param  array  $payload
     * @param  string  $key
     * @return bool
     */
    protected function valid_mac_for_key(array $payload, string $key): bool
    {
        return hash_equals($this->hash($payload['iv'], $payload['value'], $key), $payload['mac'],);
    }

    /**
     * Ensure the given tag is a valid tag given the selected cipher.
     *
     * @param  string  $tag
     * @return void
     */
    protected function ensure_tag_is_valid($tag)
    {
        if (self::$supported_ciphers[$this->cipher]['aead'] && ($tag === null || strlen($tag) !== 16)) {
            throw new Decrypt_Exception('Could not decrypt the data.');
        }

        if (! self::$supported_ciphers[$this->cipher]['aead'] && is_string($tag)) {
            throw new Decrypt_Exception('Unable to use tag because the cipher algorithm does not support AEAD.');
        }
    }

    /**
     * Determine if we should validate the MAC while decrypting.
     *
     * @return bool
     */
    protected function should_validate_mac()
    {
        return ! self::$supported_ciphers[$this->cipher]['aead'];
    }

    /**
     * {@inheritDoc}
     */
    public function get_key(): string
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function get_all_keys(): array
    {
        return [$this->key, ...$this->previous_keys];
    }

    /**
     * {@inheritDoc}
     */
    public function get_previous_keys(): array
    {
        return $this->previous_keys;
    }

    /**
     * Set the previous / legacy encryption keys that should be utilized if decryption fails.
     *
     * @param  array  $keys
     * @return $this
     */
    public function previous_keys(array $keys)
    {
        $derived = [];
        foreach ($keys as $key) {
            $derived[] = $this->derive_key($key, $this->cipher);
        }

        $this->previous_keys = $derived;
        return $this;
    }
}
