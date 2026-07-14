<?php

declare(strict_types=1);

namespace Infrastructure\Security\Services;

use Illuminate\Support\Facades\Log;

final readonly class SecureIdService
{
    private const string CONTEXT_SEPARATOR = '|';

    private string $key;

    private string $cipher;

    private int $nonceLength;

    public function __construct(
        ?string $key = null,
        string $cipher = 'aes-256-gcm',
        int $nonceLength = 12,
    ) {
        $key ??= config('secureid.key');
        $cipher ??= config('secureid.cipher', 'aes-256-gcm');
        $nonceLength ??= (int) config('secureid.nonce_length', 12);

        if (blank($key)) {
            throw new \RuntimeException(
                'Secure ID encryption key is not configured. '
                .'Run `php artisan security:generate-key` to generate one.'
            );
        }

        $this->key = str_starts_with((string) $key, 'base64:')
            ? (string) base64_decode(substr((string) $key, 7), true)
            : $key;

        if (mb_strlen($this->key, '8bit') !== 32) {
            throw new \RuntimeException('Secure ID encryption key must be 32 bytes (256-bit).');
        }

        if (! in_array($cipher, openssl_get_cipher_methods(), true)) {
            throw new \RuntimeException('Unsupported cipher: ' . $cipher);
        }

        $this->cipher = $cipher;
        $this->nonceLength = $nonceLength;
    }

    /**
     * Encrypt an ID with a context prefix.
     *
     * @param  string  $id  Raw ULID to encrypt.
     * @param  string  $context  Entity context key (e.g. 'beneficiary', 'family_card').
     * @return string URL-safe encrypted ID.
     */
    public function encrypt(string $id, string $context): string
    {
        $prefix = $this->getContextPrefix($context);
        $plaintext = $prefix.self::CONTEXT_SEPARATOR.$id;

        $nonce = random_bytes($this->nonceLength);

        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            16, // tag length
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Secure ID encryption failed.');
        }

        // Pack: nonce (12) + tag (16) + ciphertext (variable)
        $payload = $nonce.$tag.$ciphertext;

        return $this->base64urlEncode($payload);
    }

    /**
     * Decrypt an encrypted ID and verify its context prefix.
     *
     * @param  string  $encoded  URL-safe encrypted ID.
     * @param  string  $expectedContext  Expected entity context key.
     * @return string|null Decrypted ULID, or null on failure.
     */
    public function decrypt(string $encoded, string $expectedContext): ?string
    {
        try {
            $payload = $this->base64urlDecode($encoded);

            if ($payload === false || mb_strlen($payload, '8bit') < $this->nonceLength + 16) {
                return null;
            }

            $nonce = mb_substr($payload, 0, $this->nonceLength, '8bit');
            $tag = mb_substr($payload, $this->nonceLength, 16, '8bit');
            $ciphertext = mb_substr($payload, $this->nonceLength + 16, null, '8bit');

            $plaintext = openssl_decrypt(
                $ciphertext,
                $this->cipher,
                $this->key,
                OPENSSL_RAW_DATA,
                $nonce,
                $tag,
            );

            if ($plaintext === false) {
                return null;
            }

            // Verify context prefix
            $expectedPrefix = $this->getContextPrefix($expectedContext);
            $separatorPos = mb_strpos($plaintext, self::CONTEXT_SEPARATOR, 0, '8bit');

            if ($separatorPos === false) {
                return null;
            }

            $actualPrefix = mb_substr($plaintext, 0, $separatorPos, '8bit');
            $id = mb_substr($plaintext, $separatorPos + 1, null, '8bit');

            if ($actualPrefix !== $expectedPrefix) {
                // Context mismatch — log as potential attack
                Log::warning('Secure ID context mismatch', [
                    'expected' => $expectedPrefix,
                    'actual' => $actualPrefix,
                ]);

                return null;
            }

            return $id;
        } catch (\Throwable $throwable) {
            Log::error('Secure ID decryption failed', [
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get the context prefix string from config.
     */
    private function getContextPrefix(string $context): string
    {
        $prefix = config('secureid.contexts.' . $context);

        if (! is_string($prefix) || $prefix === '') {
            throw new \InvalidArgumentException('Unknown or invalid context: ' . $context);
        }

        return $prefix;
    }

    /**
     * Base64url encode (RFC 4648 §5, no padding).
     */
    private function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64url decode.
     */
    private function base64urlDecode(string $data): string|false
    {
        return base64_decode(strtr($data, '-_', '+/'), true);
    }
}
