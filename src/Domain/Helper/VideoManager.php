<?php

namespace App\Domain\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Process;

class VideoManager {

    public function __construct(
        private readonly ParameterBagInterface $params,
        private LoggerInterface $logger
    )
    {
    }

    public function saveUploadedVideo(UploadedFile $file): array
    {
        $original = $file->getClientOriginalName();
        $ext = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $safeName = bin2hex(random_bytes(8)) . '.' . $ext;

        $file->move($this->params->get('video.directory'), $safeName);

        // generate thumbnail (at 3s)
        $thumbName = $this->generateThumbnail($safeName, 3);

        return [
            'filename' => $safeName,
            'original' => $original,
            'size' => $file->getSize(),
            'mime' => $file->getClientMimeType(),
            'thumb' => $thumbName,
        ];
    }

    private function generateThumbnail(string $filename, int $second = 1): ?string
    {
        $input = escapeshellarg($this->params->get('video.directory') . DIRECTORY_SEPARATOR . $filename);
        $thumbFilename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        $output = escapeshellarg($this->params->get('video.thumbs.directory') . DIRECTORY_SEPARATOR . $thumbFilename);

        // ffmpeg -ss <second> -i input -frames:v 1 -q:v 2 output
        $cmd = ['ffmpeg', '-ss', (string)$second, '-i', $this->params->get('video.directory') . DIRECTORY_SEPARATOR . $filename, '-frames:v', '1', '-q:v', '2', $this->params->get('video.thumbs.directory') . DIRECTORY_SEPARATOR . $thumbFilename];

        $process = new Process($cmd);
        $process->setTimeout(30);

        try {
            $process->mustRun();
            return $thumbFilename;
        } catch (\Exception $e) {
            $this->logger->error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

}

// On peut ajouter une version asynchrone (Messenger + worker) pour la génération de vignettes et transcodage
// Aussi transformer l'upload pour supporter les chunks/resumable (tus.io)
/*
/*
    $file = $form->get('videoFile')->getData();
    $meta = $this->videoManager->saveUploadedVideo($file);
    $video = new Video();
    $video->setFilename($meta['filename']);
    $video->setOriginalName($meta['original']);
    $video->setSize($meta['size']);
    $video->setMimeType($meta['mime']);
    $video->setThumb($meta['thumb']);
*/