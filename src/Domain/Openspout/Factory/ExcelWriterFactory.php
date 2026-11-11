<?php

namespace App\Domain\Openspout\Factory;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use OpenSpout\Writer\WriterInterface;

class ExcelWriterFactory
{
    public static function fromExcelFormat(ExcelFormatEnum $format): WriterInterface
    {
        return match ($format) {
            ExcelFormatEnum::CSV => new \OpenSpout\Writer\CSV\Writer(),
            ExcelFormatEnum::ODS => new \OpenSpout\Writer\ODS\Writer(),
            ExcelFormatEnum::XLSX => new \OpenSpout\Writer\XLSX\Writer()
        };
    }
}