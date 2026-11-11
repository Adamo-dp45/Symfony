<?php

namespace App\Domain\Openspout\Exporter;

use App\Domain\Openspout\Enum\ExcelFormatEnum;

interface ExporterInterface
{
    public function export(array $columnNames, array $data, ExcelFormatEnum $format): \SplFileInfo; 
}