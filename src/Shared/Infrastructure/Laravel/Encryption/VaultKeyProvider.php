<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure\Laravel\Encryption;

use Copie\Shared\Domain\Ports\EncryptionKeyProviderInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SensitiveParameter;

/**
 * HashiCorp Vault key provider for production environments.
 *
 * Uses Vault's transit secrets engine to decrypt stored keys.
 * Uses Laravel's HTTP client for proper error handling.
 */
class VaultKeyProvider implements EncryptionKeyProviderInterface
{
    private readonly string $url;

    #[SensitiveParameter]
    private readonly string $token;

    private readonly string $mountPoint;

    public function __construct()
    {
        $config = config('encryption.providers.vault');

        if ($config === null) {
            throw new RuntimeException('Vault configuration not found.');
        }

        $this->url = rtrim($config['url'] ?? '', '/');
        $this->token = $config['token'] ?? '';
        $this->mountPoint = $config['mount_point'] ?? 'transit';
    }

    public function provideKey(string $context): string
    {
        $encryptedKey = config('encryption.providers.vault.encrypted_key_'.$context)
            ?? config('encryption.providers.vault.encrypted_key');

        if ($encryptedKey === null || $encryptedKey === '') {
            throw new RuntimeException(
                sprintf('Encrypted key not found for Vault context "%s".', $context)
            );
        }

        $keyName = config('encryption.providers.vault.key_name_'.$context)
            ?? config('encryption.providers.vault.key_name');

        if ($keyName === null || $keyName === '') {
            throw new RuntimeException(
                sprintf('Vault key name not found for context "%s".', $context)
            );
        }

        try {
            $response = Http::withHeaders([
                'X-Vault-Token' => $this->token,
            ])->timeout(10)->post(sprintf('%s/v1/%s/decrypt/%s', $this->url, $this->mountPoint, $keyName), [
                'ciphertext' => $encryptedKey,
            ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    sprintf('Vault returned HTTP %d: %s', $response->status(), $response->body())
                );
            }

            $data = $response->json();
            $decrypted = $data['data']['plaintext'] ?? '';

            if ($decrypted === '') {
                throw new RuntimeException(
                    sprintf('Vault returned empty plaintext for context "%s".', $context)
                );
            }

            return base64_decode((string) $decrypted, strict: true);
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Failed to decrypt key from Vault: %s', $e->getMessage()), $e->getCode(), previous: $e);
        }
    }
}
