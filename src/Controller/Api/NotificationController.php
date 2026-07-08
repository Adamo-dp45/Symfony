<?php

namespace App\Controller\Api;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/notification', name: 'notification.')]
final class NotificationController extends AbstractController
{
    #[Route('/latest', name: 'latest')]
    public function latest(NotificationRepository $notificationRepository): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        $notifications = $notificationRepository->findLatestByUser($user->getId());

        return $this->json([
            'notifications' => array_map(fn($n) => [
                'id' => $n->getId(),
                'title' => $n->getTitle(),
                'message' => $n->getMessage(),
                'isRead' => $n->isRead(),
                'createdAt' => $n->getCreatedAt()->format('Y-m-d H:i'),
            ], $notifications),
            'unreadCount' => count($notificationRepository->findUnreadByUser($user->getId())) // Ou $notificationRepository->count(['user' => $user, 'isRead' => false])
        ]);
    }

    #[Route('/lue/{id}', name: 'read', methods: ['POST'])]
    public function markRead(
        Notification $notification,
        EntityManagerInterface $em
    ): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        if ($notification->getUser() !== $user) {
            return $this->json([
                'error' => 'Accès refusé'
            ], 403);
        }

        $notification->setIsRead(true);
        $em->flush();
        return $this->json([
            'success' => true,
            'url' => $notification->getUrl()
        ]);
    }

    #[Route('/lues', name: 'read.all', methods: ['POST'])]
    public function markAllRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(['user' => $user, 'isRead' => false]);
        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/tous', name: 'all')]
    public function index(
        Request $request,
        NotificationRepository $notificationRepository
    ): Response
    {
        /**
         * @var User
         */
        $user = $this->getUser();

        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $qb = $notificationRepository->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC');

        $total = count($qb->getQuery()->getResult());
        $notifications = $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        $totalPages = ceil($total / $limit);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    /* -- NotifierService
        public function notifier(User $user, string $title, string $content, string $url): void
        {
            $notif = new Notification();
            $notif->setUser($user)
                ->setTitle($title)
                ->setIsRead(false)
                ->setContent($content)
                ->setUrl($url)
                ->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($notif);
            $this->em->flush();
        }

        public function notifierMultiple(array $users, string $title, string $content, string $url): void
        {
            foreach ($users as $user) {
                $this->notifier($user, $title, $content, $url);
            }
        }
    */

}
