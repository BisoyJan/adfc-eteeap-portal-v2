<?php

namespace App\Support;

class DocumentTypeRegistry
{
    /**
     * Get the configured list of ETEEAP application document types.
     *
     * @return array<int, array{key: string, label: string, required: bool}>
     */
    public static function all(): array
    {
        /** @var array<int, array{key: string, label: string, required: bool}> $types */
        $types = config('eteeap.document_types');

        return $types;
    }

    /**
     * Get the list of valid document type keys.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }
}
