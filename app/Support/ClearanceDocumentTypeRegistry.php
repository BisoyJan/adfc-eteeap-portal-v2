<?php

namespace App\Support;

class ClearanceDocumentTypeRegistry
{
    /**
     * Get the configured list of clearance document types.
     *
     * @return array<int, array{key: string, label: string}>
     */
    public static function all(): array
    {
        /** @var array<int, array{key: string, label: string}> $types */
        $types = config('eteeap.clearance_documents');

        return $types;
    }

    /**
     * Get the list of valid clearance document type keys.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }
}
