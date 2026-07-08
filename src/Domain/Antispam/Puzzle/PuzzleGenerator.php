<?php

namespace App\Domain\Antispam\Puzzle;

use App\Domain\Antispam\ChallengeGenerator;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Intervention\Image\Decoders\Base64ImageDecoder;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class PuzzleGenerator implements ChallengeGenerator
{
    public function __construct(
        private readonly PuzzleChallenge $challenge
    )
    {
    }

    public function generate(string $key): Response
    {
        $position = $this->challenge->getSolution($key);
        if(!$position) {
            return new Response(null, 404);
        }
        [$x, $y] = $position;
        $backgroundPath = sprintf('%s/image.png', __DIR__); // L'image 350x200 jpg
        $piecePath = sprintf('%s/frame.png', __DIR__); // !! 80x50 jpg
        /* -- Dans sa version 2 on a la fonction 'make' qui permet d'utiliser la pièce de puzzle comme masque
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($backgroundPath);
            $piece = $manager->make($piecePath);
            $hole = clone $piece;
            $piece = $image->insert($image, 'top-left', -$x, -$y)
                ->make($hole, true);
            $image->resizeCanvas(
                PuzzleChallenge::WIDTH,
                0,
                'left',
                true,
                'rgba(0, 0, 0, 0)'
            )
            ->insert($piece, 'top-right')
            ->insert($hole->opacity(60), 'top-left', $x, $y);
            return $image->response('jpg'); -- Me donne mon image original
        */
        $manager = new ImageManager(new Driver());
        $image = $manager->read($backgroundPath);
        $piece = $manager->read($piecePath);
        $hole = clone $piece;
        $piece = $image->place($piece, 'top-left', -$x, $y);
        $image->resizeCanvas(
            PuzzleChallenge::WIDTH,
            null,
            'rgba(0, 0, 0, 0)',
            'left'
        )
        ->place($piece, 'top-right')
        ->place($hole, 'top-left', $x, $y, 60);

        return new Response($image->toJpeg(), 200, [
            'Content-Type' => 'image/jpeg'
        ]);
    }
}