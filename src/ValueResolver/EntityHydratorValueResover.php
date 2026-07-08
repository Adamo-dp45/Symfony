<?php

namespace App\ValueResolver;

use App\ValueResolver\Attribute\MapHydratedEntity;
use Attribute;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class EntityHydratorValueResover implements ValueResolverInterface
{
    private readonly EntityValueResolver $entityValueResolver;

    public function  __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        ManagerRegistry $managerRegistry,
        ?ExpressionLanguage $expressionLanguage = null,
    )
    {
        $this->entityValueResolver = new EntityValueResolver($managerRegistry, $expressionLanguage, new MapEntity());
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array|\Traversable
    {
        $attribute = $argument->getAttributes(MapHydratedEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;
        if(!($attribute instanceof MapHydratedEntity)) {
            return [];
        }
        $entity = $this->entityValueResolver->resolve($request, $argument)[0] ?? null;

        if(!$entity|| $entity::class !== $argument->getType()) {
            throw new NotFoundHttpException(sprintf("'%s' object not found by '%s'", $argument->getType(), self::class));
        }

        $this->serializer->deserialize(
            $request->getContent(),
            $entity::class,
            $request->getContentTypeFormat(),
            [
                'groups' => $attribute->groups,
                AbstractNormalizer::OBJECT_TO_POPULATE => $entity
            ]
        );

        $violations = $this->validator->validate($entity, groups: $attribute->validationGroups);
        if(count($violations)) {
            throw HttpException::fromStatusCode(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                implode("\n", array_map(fn ($e) => $e->getMessage(), iterator_to_array($violations))), // iterator_to_array car violations n'est pas vraiment un tableau
                new ValidationFailedException($entity, $violations)
            );
        }

        return [
            $entity
        ];
    }

}