<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure\Laravel\Encryption;

use Aws\Kms\KmsClient;
use Copie\Shared\Domain\Ports\EncryptionKeyProviderInterface;
use Exception;
use RuntimeException;

/**
 * AWS KMS key provider for production environments.
 *
 * Uses AWS Key Management Service to decrypt a stored encrypted key.
 * The encrypted key (ciphertext blob) should be stored in .env
 * as CIPHERSWEET_KEY or a context-specific variable.
 */
class AwsKmsKeyProvider implements EncryptionKeyProviderInterface
{
    private ?KmsClient $kmsClient = null;

    public function provideKey(string $context): string
    {
        $encryptedKey = config('encryption.providers.aws.encrypted_key_'.$context)
            ?? config('encryption.providers.aws.encrypted_key');

        if ($encryptedKey === null || $encryptedKey === '') {
            throw new RuntimeException(
                sprintf('Encrypted key not found for AWS KMS context "%s".', $context)
            );
        }

        $keyId = config('encryption.providers.aws.key_id');

        if ($keyId === null || $keyId === '') {
            throw new RuntimeException('AWS KMS key_id not configured.');
        }

        try {
            $result = $this->getClient()->decrypt([
                'CiphertextBlob' => base64_decode((string) $encryptedKey, strict: true),
                'KeyId' => $keyId,
            ]);

            return (string) $result['Plaintext'];
        } catch (Exception $exception) {
            throw new RuntimeException(sprintf('Failed to decrypt key from AWS KMS: %s', $exception->getMessage()), $exception->getCode(), previous: $exception);
        }
    }

    private function getClient(): KmsClient
    {
        if ($this->kmsClient instanceof KmsClient) {
            return $this->kmsClient;
        }

        $config = config('encryption.providers.aws');

        if ($config === null) {
            throw new RuntimeException('AWS KMS configuration not found.');
        }

        $this->kmsClient = new KmsClient([
            'version' => $config['version'] ?? '2014-11-01',
            'region' => $config['region'] ?? 'ap-southeast-1',
        ]);

        return $this->kmsClient;
    }
}
