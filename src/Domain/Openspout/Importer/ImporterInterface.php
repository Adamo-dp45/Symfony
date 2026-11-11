<?php

namespace App\Domain\Openspout\Importer;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImporterInterface
{
    public function import(UploadedFile $filePath): array;
}