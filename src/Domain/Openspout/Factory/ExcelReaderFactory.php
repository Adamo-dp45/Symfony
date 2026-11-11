<?php

namespace App\Domain\Openspout\Factory;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\ODS\Reader as ODSReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

class ExcelReaderFactory
{
    public function fromExcelFormat(ExcelFormatEnum $format): ReaderInterface
    {
        return match ($format) {
            ExcelFormatEnum::CSV => new CSVReader,
            ExcelFormatEnum::ODS => new ODSReader,
            ExcelFormatEnum::XLSX => new XLSXReader
        };
    }
}