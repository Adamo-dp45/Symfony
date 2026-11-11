<?php

namespace App\Controller;

use App\Domain\Application\Service\TwilioService;
use App\Domain\Helper\MercureCookieGenerator;
use App\Domain\Service\PdfGeneratorService;
use App\DTO\SearchQueryDto;
use App\Entity\User;
use App\Form\SearchQueryType;
use App\Repository\ProductRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly ParameterBagInterface $params
    )
    {
    }

    #[Route('/', name: 'home')] // '/{_local}' - requirements: ['tab' => 'profil|password']
    public function index(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        TranslatorInterface $translator,
        SluggerInterface $slugger,
        CacheInterface $cache,
        SessionInterface $session,
        KernelInterface $kernel,
        RecipeRepository $recipeRepository,
        TwilioService $twilio,
        // RateLimiterFactoryInterface $helloLimiter,
        LoggerInterface $logger,
        UserRepository $userRepository,
        MercureCookieGenerator $mercureCookieGenerator
    ): Response
    {
        // if(!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_AGENCE')) {
        //    throw $this->createAccessDeniedException();
        // }

        // dump($this->getUser()) - dump($security->getUser()) -- Pour recupérer des informations sur l'utilisateur authentifié
        // $this->params->get('env(MAIL_ADDRESS)'); -- Pour récupérer dans env à partir des paramètres
        // $databaseUrl2 = $params->get('DATABASE_URL'); ??
        // $translator->trans('Welcome'); -- Pour traduire
        $env = $kernel->getEnvironment(); // Permet de récupérer l’environnement ou..
        $env2 = $this->getParameter('kernel.environment');
        $debug = $this->getParameter('kernel.debug'); // Permet de savoir si symfony est en mode debug en boolean

        $name = $request->query->get('name', 'Symfony'); // ?name= | Valeur par défaut
        $url = $this->generateUrl('post', [], UrlGeneratorInterface::ABSOLUTE_URL); // Génère une URL absolue
        $slug = $slugger->slug('Mon slug');

        $session = $request->getSession(); // Récupérer la session
        $session->set('name', 'John');
        $foo = $session->get('foo');

        $value = $cache->get('key', function (ItemInterface $item): string {
            $item->expiresAfter(3600);
            $computedValue = 'Cache';
            return $computedValue;
        });
        // $cache->delete('key') -- En cas d'enregistrement d'un article, ou le faire dans un évènement
        // $cache->clear();
        // $cache->deleteItems([]);
        // $cache->deleteItem('');

        /*
            $cacheFilesystem = new FilesystemAdapter('app_cache', 3600, '/tmp/cache');
            $item = $cacheFilesystem->getItem('my_key');
            if (!$item->isHit()) {
                $item->set('Hello World');
                $cacheFilesystem->save($item);
            }
            $value = $item->get();

            $cache = new FilesystemAdapter();
            $properties = $cache->get('property.latest', function (ItemInterface $item) use ($propertyRepository): array {
                $item->expiresAfter(3600); # 1H
                return $propertyRepository->findLatest(4);
            });
            $cache = new FilesystemAdapter();
            $cache->deleteItems(['property.latest']);
        */

        // $user = $this->getUser();
        // if($user) {
        //    $notifierService->notifier(
        //        $user,
        //        'Notification ',
        //        'Vous avez reçu une notification',
        //        'url'
        //    );
        // }
        // $this->renderView() - Renvoie une simple chaîne de caractères contenant le HTML généré

        $dto = new SearchQueryDto();
        $form = $this->createForm(SearchQueryType::class, $dto);
        $form->handleRequest($request);
        $results = $recipeRepository->search($dto);

        // $twilio->sendSms('+225'. $property->getUser()->getPhone(), 'Vous avez reçu une demande de contact pour le bien '. $property->getTitle());

        /*
            $key = 'Hello';
            $limiter = $helloLimiter->create($key);
            $limit = $limiter->consume(1); // Consomme un jeton à chaque échec
            if(!$limit->isAccepted()) {
                $retryAfter = $limit->getRetryAfter()->diff(new \DateTimeImmutable());
                $message = sprintf(
                    'Veuillez réessayer dans %d heure%s et %d minute%s',
                    $retryAfter->h + $retryAfter->days*24,
                    $retryAfter->h > 1 ? 's': '',
                    $retryAfter->i,
                    $retryAfter->i > 1 ? 's' : ''
                );
                $this->addFlash('warning', $message);
                return $this->redirectToRoute('app_login');
            }
            // $limiter->reset(); -- Remettre le compteur à zéro
        */

        $logger->info('Un utilisateur a accédé à la page d’accueil', []);
        $users = $userRepository->findBy([], [], 5);

        $response = $this->render('home/index.html.twig', [ // Renvoie une Response prête à être envoyée au navigateur
            'value' => $value,
            'form' => $form,
            'results' => $results,
            'users' => $users
        ]);

        /* pour tester le private
        if($this->getUser()) { // -- Mercure
            $response->headers->set('set-cookie', $mercureCookieGenerator->generate($this->getUser()));
            return $response;
        }
        */

        /* Le cache HTTP
        $response->setPublic(); cacheable par un proxy, indique que la réponse peut être mise en cache par tout le monde
            setPrivate() veut dire que seul le navigateur client peut la garder (pas un proxy)
        $response->setMaxAge(3600); le temps du cache en seconds
        $response->setSharedMaxAge(3600); Pour les caches partagés (Varnish, HttpCache)
        $response->headers->addCacheControlDirective('must-revalidate', true);
        */

        return $response;
    }

    #[Route('/intervention', name: 'intervention')]
    public function image(ImageManager $manager)
    {
        $image = $manager->read($this->getParameter('kernel.project_dir') . '/public/images/empty.jpg');
        return new Response($image->toJpeg(), 200, [
            'Content-Type' => 'image/jpeg'
        ]);
    }

    #[Route('/autocomplete', name: 'autocomplete')]
    public function autocompletion()
    {
        return $this->render('home/autocomplete.html.twig', []);
    }

    #[Route('/autocomplete/search', name: 'autocomplete.search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $query = trim($request->query->get('q', ''));
        if(strlen($query) < 2) {
            return $this->json([]);
        }
        $results = $productRepository->findBySearchQuery($query, 10);
        $suggestions = array_map(fn($item) => [ /*
            - On formate la réponse pour le front
        */
            'id' => $item->getId(),
            'label' => $item->getName(),
            'url' => '' // $this->generateUrl('app_product_show', ['id' => $item->getId()])
        ], $results);
        return $this->json($suggestions);
    }

    #[Route('/ping/{user}', name: 'ping', methods: ['POST'])]
    public function ping(
        HubInterface $hub,
        MessageBusInterface $bus,
        SerializerInterface $serializer,
        ?User $user = null,
    ): RedirectResponse
    {
        $target = ''; // On peut spécifier sur quel cible on veut envoyé
        if($user !== null) {
            $target = "http://monsite.com/user/{$user->getId()}";
        }

        $update = new Update( // Pour envoyer une notification
            [
                // 'http://monsite.com/ping',
                $target
            ], // Le topic a utilisé, peut être une chaîne de caractère ou un tableau contenant plusieurs topic
            // json_encode(['status' => 'OK']), -- Les données à envoyés au format json, ou..
            $serializer->serialize($this->getUser(), 'json', ['groups' => 'users']),
            true // Pour les ping privée
        );
        $hub->publish($update); // Permet de publier de manière sync
        // $bus->dispatch($update); -- Pour publier de manière async

        return $this->redirectToRoute('home');
    }

    #[Route('/pdf', name: 'pdf')]
    public function pdf(): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        // $options->set('isHtml5ParserEnabled', true); -- Permet d'activer le parser html5
        // $options->set('isRemoteEnabled', true); -- !! le chargement d'images distantes

        $dompdf = new Dompdf($options);
        $html = $this->renderView('home/pdf.html.twig');
        $dompdf->loadHtml($html); // Pour charger le html dans dompdf
        $dompdf->setPaper('A4', 'portrait'); // Pour configurer la taille et l'orientation du papier, on a aussi 'landscape'
        $dompdf->render();

        // $dompdf->stream("document.pdf", ["Attachment" => true]); -- Permet de télécharger le pdf, 'false' pour l'afficher dans le navigateur

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    #[Route('/output-pdf', name: 'pdf.bundle')]
    public function outputPdf(PdfGeneratorService $pdfGeneratorService): Response
    {
        $html = $this->renderView('home/pdf.html.twig');
        $content = $pdfGeneratorService->output($html); /*
            - Ou.. '->getPdf()' pour ensuite lui passer les options
        */

        return new Response($content, 200, [
            'Content-Type' => 'application/pdf',
            // 'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    #[Route('/stream-pdf', name: 'pdf.bundle.stream')]
    public function streamPdf(PdfGeneratorService $pdfGeneratorService): StreamedResponse
    {
        return $pdfGeneratorService->getStreamResponse($this->renderView('home/pdf.html.twig'), 'document.pdf');
    }

    #[Route('/ux', name: 'ux')]
    public function ux(
        ChartBuilderInterface $chartBuilder
    ): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Cookies eaten 🍪',
                    'backgroundColor' => 'rgb(255, 99, 132, .4)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [2, 10, 5, 18, 20, 30, 45],
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Km walked 🏃‍♀️',
                    'backgroundColor' => 'rgba(45, 220, 126, .4)',
                    'borderColor' => 'rgba(45, 220, 126)',
                    'data' => [10, 15, 4, 3, 25, 41, 25],
                    'tension' => 0.4,
                ],
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $this->render('home/ux.html.twig', [
            'chart' => $chart
        ]);
    }

}
