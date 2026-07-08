<?php

namespace App\ApiPlateform\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;
use Symfony\Component\Uid\Uuid;

/**
 * Permet de récupérer les données exposées par l'api depuis une base de données, un service externe, un fichier..
 */
class DependencyDataProvider implements ProviderInterface
{
    public function __construct(private DependencyRepository $dependencyRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if($operation->getName() === 'Collection') {
            return $this->dependencyRepository->findAll();
        } elseif($operation->getName() === 'Item') {
            return $this->dependencyRepository->find($uriVariables['uuid']);
        } else {
            return null;
        }
    }
}