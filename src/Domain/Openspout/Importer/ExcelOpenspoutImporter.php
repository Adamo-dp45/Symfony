<?php

namespace App\Domain\Openspout\Importer;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use App\Domain\Openspout\Factory\ExcelReaderFactory;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\SheetInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Override;

class ExcelOpenspoutImporter implements ImporterInterface
{
    public function __construct(
        private readonly ExcelReaderFactory $excelReaderFactory
    )
    {
    }

    #[Override]
    public function import(UploadedFile $file): array
    {
        $data = [];
        $extension = $file->guessExtension();
        $format = ExcelFormatEnum::fromExtension($extension);
        $reader = $this->excelReaderFactory->fromExcelFormat($format);
        $reader->open($file->getPathname());
        /**
         * @var SheetInterface $sheet
         */
        foreach($reader->getSheetIterator() as $sheet) {
            /**
             * @var Row $row
             */
            foreach($sheet->getRowIterator() as $row) {
                $data[] = $row->toArray();
            }
        }

        return $data;
    }
}