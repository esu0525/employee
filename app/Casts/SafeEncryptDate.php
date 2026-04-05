<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;

class SafeEncryptDate implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) return $value;

        try {
            $decrypted = Crypt::decryptString($value);
            return Carbon::parse($decrypted);
        } catch (DecryptException $e) {
            // Return as-is/parsed if it's not encrypted (legacy support)
            try {
                return Carbon::parse($value);
            } catch (\Exception $e2) {
                return $value;
            }
        }
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, $key, $value, $attributes)
    {
        if (empty($value)) return $value;

        // Extract value as Y-m-d string for encryption
        $dateStr = null;
        if ($value instanceof Carbon) {
            $dateStr = $value->toDateString();
        } elseif (is_string($value)) {
            try {
                $dateStr = Carbon::parse($value)->toDateString();
            } catch (\Exception $e) {
                $dateStr = $value;
            }
        }

        return Crypt::encryptString($dateStr);
    }
}
