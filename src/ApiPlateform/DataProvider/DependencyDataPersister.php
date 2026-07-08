<?php

namespace App\ApiPlateform\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

/**
 * Permet de persister les données..
 */
class DependencyDataPersister implements ProcessorInterface
{
    public function __construct(private DependencyRepository $dependencyRepository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Dependency $data */

        if($operation->getName() === 'Post' || $operation->getName() === 'Patch') {
            $this->dependencyRepository->persist($data);
        } elseif($operation->getName() === 'Delete') {
            $dependency = $this->dependencyRepository->find($uriVariables['uuid']);
            $this->dependencyRepository->delete($dependency);
        } else {
            return null;
        }
    }
}