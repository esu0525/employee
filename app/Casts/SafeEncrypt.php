<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SafeEncrypt implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) return $value;

        try {
            // Specifically check for standard Laravel encryption headers
            // Laravel-encrypted strings are typically base64(json(payload, hmac, iv))
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Return as-is if it's not encrypted (to support existing plain-text data)
            return $value;
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (empty($value)) return $value;

        // Ensure we don't double-encrypt if someone accidentally reapplies it
        try {
            Crypt::decryptString($value);
            return $value; // Already encrypted
        } catch (DecryptException $e) {
            return Crypt::encryptString($value);
        }
    }
}
