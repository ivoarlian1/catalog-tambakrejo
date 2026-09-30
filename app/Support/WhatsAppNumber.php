<?php

namespace App\Support;

class WhatsAppNumber
{
    /**
     * Normalize an Indonesian phone number to digits starting with 62.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Validate an Indonesian mobile number that can be used with WhatsApp.
     */
    public static function isValid(?string $value): bool
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return false;
        }

        return (bool) preg_match('/^628[1-9][0-9]{6,11}$/', $normalized);
    }

    /**
     * Build a wa.me URL only after the number has been validated.
     */
    public static function chatUrl(?string $value): ?string
    {
        if (! self::isValid($value)) {
            return null;
        }

        return 'https://wa.me/'.self::normalize($value);
    }
}
