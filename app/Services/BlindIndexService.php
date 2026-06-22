<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BlindIndexService
{
    /**
     * Remove all non-numeric characters from the given input.
     */
    public function normalize(string $input): string
    {
        return preg_replace('/[^0-9]/', '', $input);
    }

    /**
     * Encrypt the given plain text and return the encrypted value along with a
     * deterministic hash for blind indexing.
     *
     * The input is normalized before encryption to ensure consistency.
     *
     * @return array{encrypted: string, hash: string}
     */
    public function encrypt(string $plainText): array
    {
        $normalized = $this->normalize($plainText);

        return [
            'encrypted' => Crypt::encryptString($normalized),
            'hash' => $this->hash($normalized, false),
        ];
    }

    /**
     * Decrypt the given encrypted string back to its original value.
     */
    public function decrypt(string $encryptedText): string
    {
        return Crypt::decryptString($encryptedText);
    }

    /**
     * Generate a deterministic HMAC-SHA256 hash of the given value.
     *
     * The hash is base64-encoded. When the value has already been
     * normalized, set $shouldNormalize to false to skip normalization.
     */
    public function hash(string $plainText, bool $shouldNormalize = true): string
    {
        $text = $shouldNormalize ? $this->normalize($plainText) : $plainText;

        return base64_encode(
            hash_hmac('sha256', $text, config('app.blind_index_key'), true)
        );
    }

    /**
     * Find a model by the blind index hash of the given value.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function findByHash(string $modelClass, string $hashColumn, string $value): ?Model
    {
        return $modelClass::where($hashColumn, $this->hash($value))->first();
    }
}
