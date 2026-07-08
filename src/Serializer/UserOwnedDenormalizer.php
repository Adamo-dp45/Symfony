<?php

namespace App\Serializer;

use App\Doctrine\UserOwnedInterface;
use App\Entity\Post;
use App\Entity\User;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserOwnedDenormalizer implements DenormalizerInterface /*, DenormalizerAwareInterface */ // service.yaml
{
    /*
        - 'DenormalizerAwareInterface' permet d'utiliser les autres denormalizer
        - use DenormalizerAwareTrait; -- Ou implémenter directement
    */
    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled'; // Par défaut le denormalizer appel tous les denormalizer, va crée un boucle infinie, pour l'éviter on va utiliser ça

    public function __construct(
        private Security $security,
        private DenormalizerInterface $denormalizer // On l'a injecté dans service.yaml
    )
    {
    }

    /**
     * Va renvoyé 'true' si on supporte la denormalization
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        $reflectionClass = new ReflectionClass($type);
        $alreadyCalled = $context[$this->getAllReadyCalledKey($type)] ?? false; // Pour éviter les boucles infini
        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[$this->getAllReadyCalledKey($type)] = true;
        // $data[self::ALREADY_CALLED_DENORMALIZER] = true; -- Avec 'data' on peut avoir de meilleur performance, les 'data' vont être propre à chaque normalisaton alors que le context va être globale
        /** @var UserOwnedInterface $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        /**
         * @var User
         */
        $user = $this->security->getUser();
        $obj->setUser($user);
        return $obj;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Post::class => true // Pour limiter l'activation du denormalizer
        ];
    }

    // public function setDenormalizer(DenormalizerInterface $denormalizer): void {}

    private function getAllReadyCalledKey(string $type)
    {
        return self::ALREADY_CALLED_DENORMALIZER . $type;
    } // Avec ça si dans un 'Post' et une 'Catgery' on a une relation avec 'User' et qu'on crée un 'Post' avec une 'Category' en même temps il appartiendront à l'utilisateur
}