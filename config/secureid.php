<?php

declare(strict_types=1);

return [

    /*
    |-------------------------------------------------------------------------
    | Secure ID Encryption
    |-------------------------------------------------------------------------
    |
    | Configuration for encrypting ULID IDs in URLs using AES-256-GCM.
    | The key should be generated via `php artisan security:generate-key`.
    |
    */

    /*
     * AES-256-GCM encryption key (base64-encoded, 32 bytes).
     * Set via SECURE_ID_KEY in .env.
     * Generate with: php artisan security:generate-key
     */
    'key' => env('SECURE_ID_KEY'),

    /*
     * Cipher algorithm. Only 'aes-256-gcm' is supported.
     * GCM provides authenticated encryption (confidentiality + integrity).
     */
    'cipher' => 'aes-256-gcm',

    /*
     * Nonce (IV) length in bytes.
     * 12 bytes is the standard recommendation for GCM.
     */
    'nonce_length' => 12,

    /*
     * Context prefixes for each entity type.
     * Prevents cross-entity replay attacks: an encrypted ID for one entity
     * type cannot be used against a different entity's endpoint.
     *
     * Format: {prefix}| when stored in the encrypted payload.
     */
    'contexts' => [
        'beneficiary' => 'ben',
        'family_card' => 'fam',
        'user' => 'usr',
    ],
];
