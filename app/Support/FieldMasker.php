<?php

namespace App\Support;

class FieldMasker
{
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        $visible = min(2, (int) ceil(strlen($name) / 3));

        return substr($name, 0, $visible) . str_repeat('*', strlen($name) - $visible) . '@' . $domain;
    }

    public static function maskAddress(string $address): string
    {
        if (empty($address)) {
            return $address;
        }

        $visible = min(5, (int) ceil(strlen($address) / 4));

        return substr($address, 0, $visible) . str_repeat('*', strlen($address) - $visible);
    }
}
