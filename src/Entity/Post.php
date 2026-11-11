<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use App\ApiPlateform\Graphql\PostMaxIdResolver;
use App\Controller\Api\EmptyController;
use App\Controller\Api\PostCountController;
use App\Controller\Api\PostImageController;
use App\Controller\Api\PostPublishController;
use App\Doctrine\UserOwnedInterface;
use App\Repository\PostRepository;
use App\Serializer\Attribute\ApiAuthGroups;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Valid;
use Vich\UploaderBundle\Mapping\Attribute\Uploadable;
use Vich\UploaderBundle\Mapping\Attribute\UploadableField;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 20, // Le nombre d'items qu'on veut par page
    paginationPartial: false, // Créer une pagination qui est partielle, il ne fera pas de compteur pour compter le nombre d'enregistrement, il sera juste en mésure de nous dire voila l'url de la page suivante, mais il ne vas nous dire combien d'éléments il y'a, donc il ne peut pas nous donner l'url de la page suivante par ex, pour de la performance ça peut être intérèssant
    paginationClientItemsPerPage: true, // On autorise l'utilisateur à pouvoir choisir le nombre de page qu'il veut ce qui lui permet de taper '&itemsPerPage=100' dans l'url
    paginationMaximumItemsPerPage: 20, // Permet de limité, ici 20 même si l'utilisateur demande plus
    // paginationEnabled: false, -- Pour désactiver la pagination
    // formats: ['xml', 'jsonld', 'csv' => ['text/csv']]

    normalizationContext: [ // Permet de préciser le context de normalisation qui sera envoyer au système de normalisation de symfony, les champs qui seront affichés 'globale'
        'groups' => ['read:collection'],
        'openapi_definition_name' => 'Collection', // Explique à quoi ressemble cette entité, on peut changer le nom
        'skip_null_values' => false /*
            - Vu que 'ApiPlatform' ne renvoi pas un champ s'il est 'null' on peut désactiver son comportement ainsi
        */
    ],
    denormalizationContext: ['groups' => ['put:Post']], // Permet d'indiquer les champs qui seront modifié 'globale'
    validationContext: ['groups' => ['create:Post']], // Ou.. utiliser une méthode statique
    // validationContext: ['groups' => [Post::class, 'validationGroups']],
    order: ['createdAt' => 'DESC'], // Piloter l'ordre
    // routePrefix: '/library' -- Pour préfixer la route cette Api

    operations: [ // Va controller les opérations qui sont disponible au niveau des items
        new GetCollection(
            validationContext: ['groups' => ['create:Post']],
            normalizationContext: [
                'groups' => ['read:collection'],
                'openapi_definition_name' => 'Détail d\'article'
            ]
            // Pour n'afficher que les articles de l'utilisateur, On peut utiliser un filtre automatique ou Doctrine - CurrentUserExtension que j'ai créer
            // description: 'Permet de mettre un enregistrement en corbeille',
        ),
        new Get(
            requirements: ['id' => '\d+'],
            normalizationContext: ['groups' => ['read:collection', 'read:item', 'read:Post']]
        ),
        new MetadataPost(
            denormalizationContext: [
                'groups' => ['put:Post'], // Seule les champs marqués dans ce groupe pourront être modifiés
                'openapi_definition_name' => 'Création d\'article'
            ],
        ),
        new Put(),
        new Delete(),
        new Patch( // 'application/merge-patch+json' et veut dans son body un objet {} même vide
            uriTemplate: '/posts/{id}/publish',
            requirements: ['id' => '\d+'],
            controller: PostPublishController::class,
            // inputFormats: ['json' => ['application/merge-patch+json']], -- Pour définir le format supporté

            # Spécification OpenApi
            openapi: new Operation( // Pour personnaliser notre documentation openapi, dans le cas des propriétés #[ApiProperty]
                summary: 'Permet de publier un article',
                description: 'Publication d\'article',
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [ // Pour décrire le schéma de notre Api, les paramètre qu'on attend
                                // 'type' => 'object',
                                // 'properties' => [
                                //    'name' => ['type' => 'string'],
                                //    'description' => ['type' => 'string']
                                // ]
                            ],
                            // 'example' => [ -- Pour la partie exemple
                            //    'name' => 'Mr. Rabbit',
                            //    'description' => 'Pink Rabbit'
                            // ]
                        ]
                    ])
                )
            )
        ),
        new GetCollection(
            name: 'Articles',
            uriTemplate: '/posts/count',
            controller: PostCountController::class,
            read: false, // Pour s'assurer qu'il ne lise pas les informations vu qu'il est capable de serialize un simple entier
            filters: [], // Pour spécifier les filtres qu'on accepte
            paginationEnabled: false,
            // parameters: new Parameters([]),

            openapi: new Operation( // Pour personnaliser notre documentation openapi 
                summary: 'Récupère le nombre total d\'article',
                description: 'Filter les articles en ligne',
                /*
                    parameters: [
                        [
                            'in' => 'query', -- Le paramètre qui va se situer dans les query parameters
                            'name' => 'online',
                            'schema' => [
                                'type' => 'boolean',
                                'maximum' => 1,
                                'minimum' => 0
                            ]
                        ]
                    ],
                */
                responses: [ // Depuis API Platform 4.2+ : un objet 'Response' (et non plus un simple tableau)
                    '200' => new Response(
                        description: 'Le nombre de résultats',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3
                                ]
                            ]
                        ])
                    )
                ]
            )
        ),
        new MetadataPost( /*
            - 'multipart-form'
        */
            uriTemplate: '/posts/image/{id}', 
            controller: PostImageController::class, /*
                - Ou utiliser 'DesarializeListener', 'FileDenormalizer' et remplacé le controller par 'EmptyController::class'
            */
            deserialize: false, // 'false' si veut envoyer qu'une image dans le cadre d'une requête de type 'multipart/form-data' en utilisant notre 'PostImageController', 'true' si on veut envoyer un fichier en plus d'autres informations, le faire manuellement on vas décoder le 'DesarializeListener' de 'ApiPlatform' avec mon 'DesarializeListener' et 'FileDenormalizer'
            inputFormats: ['multipart' => ['multipart/form-data']], // Pour injecter le format dans la requête
            normalizationContext: ['groups' => ['read:collection']],
            openapi: new Operation(
                summary: 'Permet de publier un article',
                description: 'Publication d\'article',
                requestBody: new RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => ['type' => 'string'],
                                    'format' => ['type' => 'binary']
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),

        // new Get( -- Si j'ai créer ce endpoint pour juste avoir un Uri pour pouvoir le remplir du coté du Comment
        //    routePrefix: '/prefix/{id}', -- Préfixe la route
        //    uriTemplate: '/uri/{id}', -- Pour changer le nom de la route
        //    inputFormats: ['json' => ['application/merge-patch+json']] -- Permet aussi d'indiquer le header
        //    parameters: [], -- Les paramètre qu'on attend
        //    status: 301
        //    schemes: ['https'],
        //    defaults: ['color' => 'brown'],
        //    options: ['my_option' => 'my_option_value'],
        //    host: '{subdomain}.api-platform.com'
        //    controller: EmptyController::class, -- Pour retourner une reponse vide
        //    read: false, -- Pour éviter qu'il fasse des appels à la base de données, permet de ne pas avoir l'étape de lecture par défaut donc on vas se trouver avec un objet vide dans le controller qu'on pourra remplir
        //    write: false -- On aura plus la partie persistance, dans ce cas c'est au controller de le gérer
        //    deserialize: false -- Permet au listener de ne pas se lancer
        //    filters: [] -- Filtres qu'on accepte
        //    openapi: false -- Pour désactiver sa documentation
        //    security: "is_granted('EDIT_COMMENT', object)"
        //    security: "is_granted('ROLE_USER') and object.owner == user"
        //    securityMessage: 'Only admins can add books.'
        // )
    ],

    graphQlOperations: [
        new QueryCollection(
            paginationType: 'page', // Va changer la logique de récupération de notre ressource
            paginationItemsPerPage: 5
        ),
        new Query(
            // resolver: PostMaxIdResolver::class,
            // args: [ -- Permet d'avoir des arguments
            //    'id' => ['type' => 'Int!'], -- ! veut dire que c'est obligatoire
            //    'log' => ['type' => 'Boolean!', 'description' => 'Is logging activated?'],
            // ],
            // read: false
        ),
        new Mutation(
            name: 'create',
            validationContext: ['groups' => ['create:Post']],
            security: "is_granted('ROLE_USER')"
        ),
        new Mutation(name: 'update'),
        new DeleteMutation(name: 'delete')
    ]
),
ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial'
]),
ApiFilter(OrderFilter::class, properties: ['id', 'title']),
ApiAuthGroups([ // Pour le 'PostAPINormalizer', On le fais pour avoir quelque chose de générique
    'POST_EDIT' => ['read:collection:Owner'],
    'ROLE_USER' => ['read:collection:User'],
])] /* - On a aussi 'BooleanFilter', 'RangeFilter', 'DateFilter', 'OrderFilter'
        - 'exact' l'utilisateur doit entrer exactement la valeur qui correspond au niveau de la base de données
        - 'partial' par défaut, contient la chaîne entrée
        - 'start' commence par la chaîne
        - 'end' se termine par la chaîne
        - 'word_start' commence par la chaîne ou précédé d'un espace
    - 'OrderFilter' permet de spécifié l'ordre de récupération des éléments
*/
#[Uploadable()]
class Post implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection', 'read:full:comment', 'read:comment'])] // ApiPlateform
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read:collection', 'read:item', 'put:Post']),
        Assert\Length(min: 3, groups: ['create:Post'])
    ] // Validation avec Api Plateforme, On peut ajouter aussi des groupes de validations
    private ?string $title = null;

    #[ORM\Column(length: 255)] // read:collection:User pour mon PostContextBuilder, pour qu'un utilisateur connecté est accès à plus de champs qu'un utilisateur non connecté
    #[
        Groups(['read:collection:User', 'put:Post']),
        ApiProperty(openapiContext: [
            'type' => 'string',
            'description' => 'Slug de l\'article'
        ])
    ] // 'ApiProperty' pour définir ma documentation openapi
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:collection', 'read:item', 'put:Post'])]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts', cascade: ['persist'])] // cascade: ['persist'] -- Si il y'a une catégorie qui va être associé on autorise la persistance en cascade
    #[
        Groups(['read:item', 'put:Post']),
        Valid()
    ] // Valid() -- Dans le cas ou on veut créer un article en même temps une catégorie et qu'on veut que la catégorie soit aussi valide, va utiliser le validator de la Category
    private ?Category $category = null;

    #[ORM\Column(options: ['default' => false])]
    #[
        Groups(['read:collection:Owner']),
        ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'En ligne ou pas'])
    ] // online sera une propriété de notre api, ApiProperty : Permettra d'améliorer la lisibilé de notre Api - schema
    private ?bool $online = false;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[UploadableField(mapping: 'posts', fileNameProperty: 'image')]
    #[Groups(['put:Post'])] // Pour que le 'DesarializeListener' puisse écrire
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:collection'])] // Vu qu'on veut le chemin vers l'image on a utiliser 'DesarializeListener' et 'FileDeserializer'
    private ?string $fileUrl = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'posts')]
    private Collection $tags;

    private string $currentState /* = 'draft' */;

    private ?\DateTime $reviewedAt; // Add getters et settters

    public function __construct()
    {
        // Api Plateforme : Pour ne pas qu'il affiche l'erreur de la date de création est null
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): static
    {
        $this->online = $online;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile($imageFile): static
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }

        return $this;
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Permet de renvoyer la liste des groupes de validations
     */
    public static function validationGroups(self $post)
    {
        return [
            'create:Post'
        ];
    }
}
