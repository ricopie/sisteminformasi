<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Ports;

use RuntimeException;

/**
 * Port for providing encryption keys.
 *
 * This is a framework-agnostic contract. Implementations
 * may use .env, AWS KMS, Vault, or any other key source.
 */
interface EncryptionKeyProviderInterface
{
    /**
     * Provide the encryption key for the given context.
     *
     * @param  string  $context  Identifies which encryption context (e.g., 'pii', 'financial')
     * @return string The encryption key
     *
     * @throws RuntimeException If key cannot be provided
     */
    public function provideKey(string $context): string;
}
