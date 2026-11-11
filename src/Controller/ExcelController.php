<?php

namespace App\Controller;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use App\Domain\Openspout\Exporter\ExcelOpenspoutExporter;
use App\Domain\Openspout\Form\ExcelExportType;
use App\Domain\Openspout\Form\ExcelImportType;
use App\Domain\Openspout\Importer\ExcelOpenspoutImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ExcelController extends AbstractController
{
    public function __construct(
        private readonly ExcelOpenspoutExporter $excelExporter,
        private readonly ExcelOpenspoutImporter $excelImporter
    )
    {
    }

    #[Route('/excel/import', name: 'excel.import')]
    public function import(Request $request): Response
    {
        $form = $this->createForm(ExcelImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile
             */
            $file = $form->get('file')->getData();
            $data = $this->excelImporter->import($file);

        }

        return $this->render('excel/import.html.twig', [
            'form' => $form,
            'data' => $data ?? []
        ]);
    }

    #[Route('/excel/export', name: 'excel.export')]
    public function export(Request $request): Response
    {
        $form = $this->createForm(ExcelExportType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /**
             * @var ExcelFormatEnum
             */
            $format = $form->get('format')->getData();
            $colomnNames = ['Nom', 'Prénom', 'Email'];
            $data = [
                ['Doe', 'John', 'john.doe@example.com'],
                ['Smith', 'Jane', 'jane.smith@example.com']
            ];
            $response = new StreamedResponse(fn () => $this->excelExporter->export(
                $colomnNames,
                $data,
                $format
            ));
            $response->headers->set('Content-Type', $format->contentType()); /*
                - On définit le format en fonction du contentType du format choisi (XLSX, CSV, ODS)
            */
            return $response;
        }

        return $this->render('excel/export.html.twig', [
            'form' => $form
        ]);
    }
}
