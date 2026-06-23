<?php

namespace App\Traits;

use App\Services\BlindIndexService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/** 
 * Service for handling blind indexing of sensitive data.
 * This service is no longer supported by development, 
 * and is scheduled for removal instead use spatie/laravel-ciphersweet.
 * Please check the installation method at https://github.com/spatie/laravel-ciphersweet#installation
 * 
 * @mixin Model
 *
 * @property array $blindIndexFields
 */
trait HasBlindIndex
{
    /**
     * Initialize the blind index fields for the model.
     *
     * Automatically hides the encrypted and hash columns from
     * array and JSON serialization.
     */
    public function initializeHasBlindIndex(): void
    {
        foreach ($this->blindIndexFields as $field) {
            $this->makeHidden($field . '_encrypted');
            $this->makeHidden($field . '_hash');
        }
    }

    /**
     * Boot the blind index trait for the model.
     *
     * Registers "creating" and "updating" event listeners that
     * encrypt the specified fields and generate their blind index
     * hashes before persisting to the database.
     */
    public static function bootHasBlindIndex(): void
    {
        $handle = function ($model) {
            $service = App::make(BlindIndexService::class);

            foreach ($model->blindIndexFields as $field) {
                $plain = $model->getAttributeFromArray($field);
                if (is_null($plain)) {
                    continue;
                }

                $result = $service->encrypt($plain);

                $model->attributes[$field . '_encrypted'] = $result['encrypted'];
                $model->attributes[$field . '_hash'] = $result['hash'];

                unset($model->attributes[$field]);
            }
        };

        static::creating($handle);
        static::updating($handle);
    }

    /**
     * Get an attribute from the model.
     *
     * If the attribute is a blind indexed field, the original
     * plain text value is decrypted and returned.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->blindIndexFields)) {
            $encryptedValue = $this->getAttributeFromArray($key . '_encrypted');

            if (is_null($encryptedValue)) {
                return null;
            }

            return App::make(BlindIndexService::class)->decrypt($encryptedValue);
        }

        return parent::getAttribute($key);
    }
}
