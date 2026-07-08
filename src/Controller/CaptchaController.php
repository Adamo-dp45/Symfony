<?php

namespace App\Controller;

use App\Domain\Antispam\ChallengeGenerator;
use App\Domain\Antispam\ChallengeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CaptchaController extends AbstractController
{
    #[Route('/captcha', name: 'captcha')]
    public function captcha(
        Request $request,
        ChallengeGenerator $generator,
        // ChallengeInterface $challenge -- Pour tester
    ): Response
    {
        // $key = $challenge->generateKey();
        return $generator->generate($request->query->get('challenge', ''));
    }

}
