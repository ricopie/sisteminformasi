<?php

namespace App\Traits;

use App\Services\BlindIndexService;
use Illuminate\Support\Facades\App;

trait HasBlindIndex
{
    public static function bootHasBlindIndex(): void
    {
        $handle = function ($model) {
            $service = App::make(BlindIndexService::class);

            foreach ($model->blindIndexFields ?? [] as $field) {
                $plain = $model->getAttributeFromArray($field);
                if (is_null($plain)) {
                    continue;
                }

                $result = $service->encrypt($plain);

                $model->{$field . '_encrypted'} = $result['encrypted'];
                $model->{$field . '_hash'} = $result['hash'];
                unset($model->{$field});
            }
        };

        static::creating($handle);
        static::updating($handle);
    }

    public function getNikAttribute(): ?string
    {
        if (!in_array('nik', $this->blindIndexFields ?? [])) {
            return null;
        }

        if (is_null($this->nik_encrypted)) {
            return null;
        }

        return App::make(BlindIndexService::class)->decrypt($this->nik_encrypted);
    }

    public function getFamilyCardNumberAttribute(): ?string
    {
        if (!in_array('family_card_number', $this->blindIndexFields ?? [])) {
            return null;
        }

        if (is_null($this->family_card_number_encrypted)) {
            return null;
        }

        return App::make(BlindIndexService::class)->decrypt($this->family_card_number_encrypted);
    }
}
