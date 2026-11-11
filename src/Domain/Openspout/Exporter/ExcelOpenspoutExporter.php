<?php

namespace App\Domain\Openspout\Exporter;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use App\Domain\Openspout\Factory\ExcelWriterFactory;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use Override;

class ExcelOpenspoutExporter implements ExporterInterface
{
    public function __construct(
        private readonly ExcelWriterFactory $excelWriterFactory
    )
    {
    }

    #[Override]
    public function export(array $columnNames, array $data, ExcelFormatEnum $format): \SplFileInfo
    {
        $filePath = "export.{$format->extension()}";
        $rows = [Row::fromValues($columnNames, (new Style())->fontBold)]; // '->fontBold' ne fonctionne pas
        foreach ($data as $row) {
            $rows[] = Row::fromValues($row);
        }

        /* On doit récupérer un writter en fonction du format choisi (XLSX, CSV, ODS) et écrire les lignes dans le fichier. on.. 
         */
        $writer = $this->excelWriterFactory->fromExcelFormat($format);
        $writer->openToBrowser($filePath);
        $writer->addRows($rows);
        $writer->close();
        /*
            $writer->openToFile($format); -- Pour ouvrir le fichier dans le navigateur
            $writer->openToBrowser($format); -- Pour streamer le fichier dans le navigateur
        */
        return new \SplFileInfo($filePath);
    }
}