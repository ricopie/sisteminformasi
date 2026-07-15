<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Encryption Key Provider
    |--------------------------------------------------------------------------
    |
    | Select which key provider your application will use.
    |
    | Supported: "env", "aws", "vault"
    |
    */
    'key_provider' => env('ENCRYPTION_KEY_PROVIDER', 'env'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Key
    |--------------------------------------------------------------------------
    |
    | The encryption key used when no cloud provider is configured.
    | This is the default key from .env file.
    |
    */
    'fallback_key' => env('CIPHERSWEET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cloud Providers
    |--------------------------------------------------------------------------
    |
    | Configure your cloud key management services here.
    | Only the active provider (based on key_provider) will be used.
    |
    */
    'providers' => [
        'aws' => [
            'key_id' => env('AWS_KMS_KEY_ID'),
            'encrypted_key' => env('AWS_KMS_ENCRYPTED_KEY'),
            'region' => env('AWS_KMS_REGION', 'ap-southeast-1'),
            'version' => '2014-11-01',
        ],

        'vault' => [
            'url' => env('VAULT_URL'),
            'token' => env('VAULT_TOKEN'),
            'mount_point' => env('VAULT_MOUNT_POINT', 'transit'),
            'key_name' => env('VAULT_KEY_NAME'),
            'encrypted_key' => env('VAULT_ENCRYPTED_KEY'),
        ],
    ],
];
