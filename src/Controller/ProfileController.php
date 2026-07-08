<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\DeleteAccountFormType;
use App\Form\UserAvatarFormType;
use App\Form\UserEditFormType;
use App\Form\UserPreferencesFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/profile', name: 'profile.')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private TokenStorageInterface $tokenStorage,
        private SluggerInterface $slugger
    ) {
    }

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
         /**
         * @var User
         */
        $user = $this->getUser();

        [$deleteForm, $response] = $this->delete($request, $user);
        if ($response) {
            return $response;
        }

        [$userEditForm, $response] = $this->edit($request, $user);
        if ($response) {
            return $response;
        }

        [$passwordForm, $response] = $this->password($request, $user);
        if ($response) {
            return $response;
        }

        [$avatarForm, $response] = $this->avatar($request, $user);
        if ($response) {
            return $response;
        }

        [$preferForm, $response] = $this->preferences($request, $user);
        if ($response) {
            return $response;
        }

        return $this->render('profile/index.html.twig', [
            'deleteForm' => $deleteForm->createView(),
            'userEditForm' => $userEditForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'avatar' => $avatarForm->createView(),
            'preferForm' => $preferForm->createView()
        ]);
    }

    public function delete(
        Request $request,
        User $user
    ): array {
        $form = $this->createForm(DeleteAccountFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                // $form->get('password')->addError(new FormError("Mot de passe incorrect")); -- Ne s'affiche pas à cause de Turbo
            } else {
                $this->em->remove($user);
                $this->em->flush();
                $this->tokenStorage->setToken(null); // Déconnecter l'utilisateur proprement
                $this->addFlash('success', 'Votre compte a été supprimer avec succès');
                return [$form, $this->redirectToRoute('profile.index')];
            }
        }
        return [$form, null];
    }

    public function edit(
        Request $request,
        User $user
    ): array
    {
        $form = $this->createForm(UserEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newEmail = $user->getPendingEmail();

            if ($newEmail && $newEmail !== $user->getEmail()) {
                // Générer un token unique
                $token = Uuid::v4()->toRfc4122();
                $user->setEmailChangeToken($token);
                $user->setEmailChangeRequestedAt(new DateTimeImmutable());

                // Envoi du mail de confirmation
                $url = $this->urlGenerator->generate('profile.confirm_email', [
                    'token' => $token,
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('no-reply@tonsite.fr')
                    ->to($newEmail)
                    ->subject('Confirmez votre nouvelle adresse email')
                    ->html("<p>Bonjour,</p><p>Merci de confirmer votre nouvelle adresse email en cliquant ici :</p><p><a href='$url'>$url</a></p>");
                $this->mailer->send($email);
                $this->addFlash('success', 'Un email de confirmation a été envoyé à votre nouvelle adresse.');
            }

            $this->em->flush();
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return [$form, $this->redirectToRoute('profile.index')];
        }
        return [$form, null];
    }

    #[Route('/confirmation-email/{token}', name: 'confirm_email')]
    public function confirmEmail(string $token, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['emailChangeToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        ## Vérifier expiration
        $requestedAt = $user->getEmailChangeRequestedAt();
        $expiresAt = $requestedAt?->modify('+30 minutes'); // Appelle la méthode modify uniquement si $requestedAt n’est pas null, Si il est null renvoie null sans provoquer d’erreur

        if (!$requestedAt || new DateTimeImmutable() > $expiresAt) {
            $user->setPendingEmail(null);
            $user->setEmailChangeToken(null);
            $user->setEmailChangeRequestedAt(null);
            $em->flush();

            $this->addFlash('danger', 'Le lien a expiré. Veuillez recommencer la procédure.');
            return $this->redirectToRoute('profile.index');
        }

        $user->setEmail($user->getPendingEmail());
        $user->setPendingEmail(null);
        $user->setEmailChangeToken(null);
        $user->setEmailChangeRequestedAt(null);
        $em->flush();

        $this->addFlash('success', 'Votre nouvelle adresse email a été confirmée.');
        return $this->redirectToRoute('profile.index');
    }

    public function password(
        Request $request,
        User $user
    ): array {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(new FormError("Mot de passe actuel incorrect"));
            } else {
                $newPassword = $form->get('newPassword')->getData();
                $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));

                $this->em->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');
                return [$form, $this->redirectToRoute('profile.index')];
            }
        }
        return [$form, null];
    }

    public function avatar(
        Request $request,
        User $user
    ): array
    {
        $form = $this->createForm(UserAvatarFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile
             */
            $file = $request->files->get('avatar'); // Ou .. $form->get('avatar')->getData()
            $path = $this->getParameter('avatars.directory');

            if($file) {
                // Directory exist
                // Suppression de l'ancien avatar
                $oldAvatar = $user->getAvatar();
                if ($oldAvatar && file_exists($path . '/' . $oldAvatar)) {
                    $oldPath = $path . '/' . $oldAvatar;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                try {
                    // $filename = $this->imageManager->move($file, $path, 400, 400);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Upload failed: ' . $e->getMessage());
                    return [$form, $this->redirectToRoute('profile.index')];
                }

                //$user->setAvatar($filename);
                $this->em->flush();
                $this->addFlash('success', 'Avatar redimensionné avec proportions conservées');
                return [$form, $this->redirectToRoute('profile.index')];
            }
        }
        return [$form, null];
    }

    #[Route('/avatar/delete', name: 'avatar.delete', methods: ['POST'])]
    public function deleteAvatar(
        Request $request,
        EntityManagerInterface $em
    ): RedirectResponse {
        /**
         * @var User
         */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete_avatar', $request->request->get('_token'))) {
            $oldAvatar = $user->getAvatar();

            if ($oldAvatar) {
                $filePath = $this->getParameter('avatars.directory') . '/' . $oldAvatar;

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $user->setAvatar(null);
                $em->flush();
                $this->addFlash('success', 'Avatar supprimé avec succès');
            }
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide');
        }
        return $this->redirectToRoute('profile.index');
    }

    public function preferences(
        Request $request,
        User $user
    ): array
    {
        $form = $this->createForm(UserPreferencesFormType::class, [
            'theme' => $user->getPreference('theme', 'light'),
            'notifications' => $user->getPreference('notifications', false),
            'language' => $user->getPreference('language', 'fr'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPreferences($data);
            $this->em->flush();

            $this->addFlash('success', 'Préférences mises à jour.');
            return [$form, $this->redirectToRoute('profile.index')];
        }
        return [$form, null];
    }

}
