<?php

namespace App\Services;

use App\Models\FamilyCard;
use App\Models\FosterChild;
use Illuminate\Support\Facades\Crypt;

class BlindIndexService
{
    public function normalize(string $input): string
    {
        return preg_replace('/[^0-9]/', '', $input);
    }

    public function encrypt(string $plainText): array
    {
        $normalized = $this->normalize($plainText);

        return [
            'encrypted' => Crypt::encryptString($normalized),
            'hash' => $this->hash($normalized),
        ];
    }

    public function decrypt(string $encryptedText): string
    {
        return Crypt::decryptString($encryptedText);
    }

    public function hash(string $plainText): string
    {
        return base64_encode(
            hash_hmac('sha256', $this->normalize($plainText), config('app.blind_index_key'), true)
        );
    }

    public function findFosterChildByNik(string $nik): ?FosterChild
    {
        return FosterChild::where('nik_hash', $this->hash($nik))->first();
    }

    public function findFamilyCardByNumber(string $number): ?FamilyCard
    {
        return FamilyCard::where('family_card_number_hash', $this->hash($number))->first();
    }
}
