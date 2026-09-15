<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Webshr\Core\Application;
use Webshr\Core\Utility\Encryption;

class EncryptionTest extends TestCase
{
    private const KEY_64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    private function buildApp(
        ?string $key = null,
        string $cipher = 'aes-256-cbc'
    ): Application {
        $encryption = ['cipher' => $cipher];
        if ($key !== null) {
            $encryption['key'] = $key;
        }

        return new Application(
            encryption: $encryption,
            base_path: __DIR__ . '/fixtures/theme',
            paths: [
                'base' => __DIR__ . '/fixtures/theme',
                'app' => __DIR__ . '/fixtures/theme/app',
                'config' => __DIR__ . '/fixtures/theme/config',
                'resources' => __DIR__ . '/fixtures/theme/resources',
                'storage' => __DIR__ . '/fixtures/theme/storage',
                'bootstrap' => __DIR__ . '/fixtures/theme/bootstrap',
                'env' => __DIR__ . '/fixtures/theme',
            ],
        );
    }

    /**
     * C2: When app.key is null, the Encryption constructor throws a
     * RuntimeException with a message mentioning the encryption key,
     * not a raw TypeError from the type system.
     */
    public function test_missing_key_throws_runtime_exception_with_clear_message(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/encryption key/i');
        $this->buildApp(null);
    }

    /**
     * M1 compat: A 64-byte key (WordPress salt length) encrypts and
     * decrypts successfully via the key derivation path.
     */
    public function test_sixty_four_byte_key_round_trips(): void
    {
        $app = $this->buildApp(self::KEY_64);
        $enc = $app->get('encryption');
        $this->assertInstanceOf(Encryption::class, $enc);

        $payload = $enc->encrypt_string('round-trip test');
        $this->assertSame('round-trip test', $enc->decrypt_string($payload));
    }

    /**
     * M1 compat: A ciphertext produced by the unmodified Encryption
     * class must still decrypt after any refactoring.
     */
    public function test_payload_encrypted_before_refactor_still_decrypts(): void
    {
        $fixture = json_decode(
            file_get_contents(
                __DIR__ . '/fixtures/encryption/aes-256-cbc-64byte-key.json'
            ),
            true
        );

        $app = $this->buildApp($fixture['key']);
        $enc = $app->get('encryption');
        $this->assertInstanceOf(Encryption::class, $enc);

        $decrypted = $enc->decrypt_string($fixture['payload']);
        $this->assertSame($fixture['plaintext'], $decrypted);
    }

    /**
     * M1 reorder: An unsupported cipher name throws a RuntimeException
     * naming the supported ciphers.
     */
    public function test_unsupported_cipher_throws(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/[Ss]upported/');
        $this->buildApp(self::KEY_64, 'aes-512-xyz');
    }

    /**
     * M1 policy unification: A previous key whose byte length does not
     * match the cipher is derived (not rejected) so decryption using a
     * rotated 64-byte WordPress salt still works.
     */
    public function test_previous_key_with_wrong_length_is_derived_not_rejected(): void
    {
        $app = $this->buildApp(self::KEY_64);
        $enc = $app->get('encryption');
        $this->assertInstanceOf(Encryption::class, $enc);

        $previous = 'ANOTHER64CHARKEYxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $this->assertSame(64, strlen($previous));

        // After the fix, this must NOT throw. The wrong-length key
        // should be derived just like the primary key.
        $enc->previous_keys([$previous]);
        $this->assertCount(1, $enc->get_previous_keys());
    }
}
