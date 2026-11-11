<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const VIEW = 'RECIPE_VIEW';
    public const CREATE = 'RECIPE_CREATE';
    public const LIST = 'RECIPE_LIST';
    public const LIST_ALL = 'RECIPE_ALL';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL])) || (in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Recipe);
    }

    /**
     * 
     * @param string $attribute
     * @param Recipe|null $subject // Ce qui me permet de supprimer la 
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
    */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) // Car moi je veux l'utilisateur, pas UserInterface qui contient qu'une méthode identifier
        {
            return false;
        }
        /* Sinon elle va me bloquer car dans les autres je n'est pas de sujet
        if(!$subject instanceof Recipe) {
            return false; // Ce n'est pas censé arriver, mais on ne sait jamais
        }
        */
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();
            /*
            Le LIST_ALL par defaut retourne false, car ici je ne lui est rien assigné sans ça
            // Mais ici j'ai préférer AdminVoter
            case self::LIST_ALL:
                return $security->isGranted('ROLE_ADMIN'); Pour vérifié avec le Security
                break;
            */
            case self::VIEW:
            case self::CREATE:
            case self::LIST:
                return true; // Si on n'est un utilisateur connecté
        }

        return false;
    }
}
