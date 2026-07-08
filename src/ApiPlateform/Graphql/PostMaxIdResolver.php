<?php

namespace App\ApiPlateform\Graphql;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Repository\PostRepository;

class PostMaxIdResolver /* implements QueryCollectionResolverInterface */ /*, MutationResolverInterface */ { // QueryCollectionResolverInterface pour les collections, MutationResolverInterface pour les mutations

    public function __construct(private PostRepository $postRepository)
    {
    }

    // public function __invoke(iterable $collection, array $context): iterable
    // {
    //    return $this->postRepository->findWithMaxId($context['args']['id']);
    // }
}