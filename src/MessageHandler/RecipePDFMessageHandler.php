<?php

namespace App\MessageHandler;

use App\Message\RecipePDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler] // Permet au système de service de savoir que lorsqu'on vas envoyé un message ce handler soit utiliser
final class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdf')] // Ou utiliser le service.yaml pour lui expliquer comment construire cet objet
        private readonly string $path,
        #[Autowire('%app.gotenberg_endpont%')]
        private readonly string $gotenbergEndpoint,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function __invoke(RecipePDFMessage $message): void
    {
        /*
            $process = new Process([ -- Permet d'exécuter des commandes systèmes
                'curl',
                '--request', -- Pour avoir des informations sur la requête
                'POST',
                $this->gotenbergEndpoint . '/forms/chromium/convert/url', -- Url serveur gotenberg si j'en ai
                '--form', -- On vas lui donner les informations
                'url=' . $this->urlGenerator->generate('recipes.show', ['id' => $message->id], UrlGeneratorInterface::ABSOLUTE_URL), -- Url à contacté
                '-o',
                $this->path . '/' . $message->id . '.pdf' -- Ou est ce qu'il va sauvegarder le pdf ou sprintf("%s/%s.pdf, $this->path, $message->id)
            ]);
            $process->run();
            if(!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        */

        // file_put_contents($this->path . '/' . $message->id . '.pdf', 'Contenu');
    }

}
