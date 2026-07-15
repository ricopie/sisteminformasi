<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure\Laravel\Encryption;

use Copie\Shared\Domain\Ports\EncryptionKeyProviderInterface;
use RuntimeException;

/**
 * Default key provider that reads from .env configuration.
 *
 * This is the fallback provider when no cloud KMS is configured.
 * Reads the encryption key from config('encryption.fallback_key')
 * which maps to CIPHERSWEET_KEY in .env.
 */
class EnvKeyProvider implements EncryptionKeyProviderInterface
{
    public function provideKey(string $context): string
    {
        $key = config('encryption.fallback_key');

        if ($key === null || $key === '') {
            throw new RuntimeException(
                sprintf('Encryption key not found. Check CIPHERSWEET_KEY in your .env file. Requested context: "%s".', $context)
            );
        }

        return $key;
    }
}
