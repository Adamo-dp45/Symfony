<?php

namespace App\Domain\Openspout\Enum;

enum ExcelFormatEnum
{
    case CSV;

    case ODS;

    case XLSX;

    public function contentType(): string
    {
        return match($this) {
            self::CSV => 'text/csv',
            self::ODS => 'application/vnd.oasis.opendocument.spreadsheet',
            self::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
    }

    public function extension(): string
    {
        return match($this) {
            self::CSV => 'csv',
            self::ODS => 'ods',
            self::XLSX => 'xlsx',
        };
    }

    public static function fromExtension(?string $extension): ?ExcelFormatEnum
    {
        return match($extension) {
            'csv' => self::CSV,
            'ods' => self::ODS,
            'xlsx' => self::XLSX,
            default => null
        };
    }
}