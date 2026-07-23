<?php

declare(strict_types=1);

final class ApiKeyEncryption
{
    private const VERSION = 'v1';

    /**
     * Encrypt an API key for database storage.
     *
     * $context should identify what the secret belongs to, such as:
     * "adminproperty:123:gemini"
     *
     * The context is authenticated but not encrypted. You do not need
     * to store it inside the encrypted payload, but you must provide
     * exactly the same context when decrypting.
     */
    public static function encrypt(string $apiKey, string $context): string
    {
        $key = self::getEncryptionKey();

        $nonce = random_bytes(
            SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES
        );

        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $apiKey,
            $context,
            $nonce,
            $key
        );

        $payload = $nonce . $ciphertext;

        return self::VERSION . ':' . sodium_bin2base64(
            $payload,
            SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
        );
    }

    public static function decrypt(string $storedValue, string $context): string
    {
        [$version, $encodedPayload] = array_pad(
            explode(':', $storedValue, 2),
            2,
            null
        );

        if ($version !== self::VERSION || $encodedPayload === null) {
            throw new RuntimeException('Unsupported encrypted API-key format.');
        }

        try {
            $payload = sodium_base642bin(
                $encodedPayload,
                SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
            );
        } catch (SodiumException $exception) {
            throw new RuntimeException(
                'The encrypted API-key value is malformed.',
                previous: $exception
            );
        }

        $nonceLength =
            SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;

        if (strlen($payload) <= $nonceLength) {
            throw new RuntimeException('The encrypted API-key value is invalid.');
        }

        $nonce = substr($payload, 0, $nonceLength);
        $ciphertext = substr($payload, $nonceLength);

        $plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $ciphertext,
            $context,
            $nonce,
            self::getEncryptionKey()
        );

        if ($plaintext === false) {
            throw new RuntimeException(
                'API-key decryption failed. The data may have been altered.'
            );
        }

        return $plaintext;
    }

    private static function getEncryptionKey(): string
    {
        $encodedKey = getenv('API_ENCRYPTION_KEY');

        if ($encodedKey === false || $encodedKey === '') {
            throw new RuntimeException(
                'API_ENCRYPTION_KEY is not configured.'
            );
        }

        $key = base64_decode($encodedKey, true);

        if (
            $key === false ||
            strlen($key) !==
                SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES
        ) {
            throw new RuntimeException(
                'API_ENCRYPTION_KEY is not a valid base64-encoded key.'
            );
        }

        return $key;
    }
}