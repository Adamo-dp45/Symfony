<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiPlateform\DataProvider\DependencyDataPersister;
use App\ApiPlateform\DataProvider\DependencyDataProvider;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV5;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    paginationEnabled: false,
    provider: DependencyDataProvider::class, /*
        - Va fonctionner pour le 'get' et 'getCollection'
    */
    processor: DependencyDataPersister::class, /*
        - !! pour le 'post', 'put' et 'delete'
    */
    operations: [
        new GetCollection(
            name: 'Collection'
        ),
        new Get(name: 'Item'), /*
            - Si on essai d'en créer il va le faire mais on n'aura pas la persistance donc 'DependencyDataPersister' pour 'post'
        */
        new Post(
            name: 'Post'
        ),
        new Patch(
            name: 'Patch',
            // requirements: ['uuid' => '\d+'], -- Mais en 'string'
            denormalizationContext: ['groups' => 'write:Dependency']
        ), /*
            - Si on n'a pas le 'setVersion' il ne pourra pas modifier la propriété
        */
        new Delete(
            name: 'Delete'
        )
    ] /*
        - Pour le 'ApiFilter(SearchFilter::class)' on ne peut pas l'utiliser vu que ça provient de 'Doctrine'
            - Sol 1 : On gère le 'query parameter' dans le 'provider'
            - Sol 2 : On crée un filtre spécifique pour la situation
    */
)]
class Dependency {

    #[ApiProperty(
        identifier: true
    )] // Va servir d'indentifieur vu que 'ApiPlatform' à besoin d'un 'id'
    private string $uuid;

    #[
        ApiProperty(
            description: 'Nom de la dépendance'
        ),
        Length(min: 1),
        NotBlank()
    ]
    private string $name;

    #[
        ApiProperty(
            description: 'Version de la dépendance',
            openapiContext: [
                'example' => '5.2.*'
            ]
        ),
        Length(min: 1),
        NotBlank(),
        Groups('write:Dependency')
    ]
    private string $version;

    public function __construct(
        // string $uuid,
        string $name,
        string $version
    )
    {
        $this->uuid = RamseyUuid::uuid5(RamseyUuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}