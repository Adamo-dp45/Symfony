<?php

namespace App\Domain\Service;

use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfGeneratorService
{
    public function __construct(
        private readonly DompdfFactoryInterface $factory, // Ou..
        private readonly DompdfWrapperInterface $wrapper
    )
    {
    }

    /**
     * La 1ère méthode générer le pdf
     */
    public function output(string $html): string
    {
        $dompdf = $this->factory->create();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }

    /**
     * Avec lui on n'a pas besoin de personnaliser le render de dompdf
     */
    public function getPdf(string $html, array $options = []): string
    {
        return $this->wrapper->getPdf($html, [...$options, [
            // 'debugPng' => true,
            // 'debugKeepTemp' => true,
            // 'debugCss' => true,
            'debugLayout' => true,
            'debugLayoutLines' => true,
            'debugLayoutBlocks' => true,
            'debugLayoutInline' => true,
            'debugLayoutPaddingBox' => true, /*
                - Peut être utile pour le débogage, mais il est recommandé de le désactiver en production pour des raisons de sécurité et de performance.
            */
        ]]);
    }


    public function getStreamResponse(string $html, string $filename = 'document.pdf', array $options = []): StreamedResponse
    {
        return $this->wrapper->getStreamResponse($html, $filename, $options)->send();
    }

}