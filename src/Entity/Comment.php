<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use App\Controller\Api\CommentCreateController;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:comment']],
    paginationItemsPerPage: 5,
    paginationPartial: false,
    order: ['publishedAt' => 'DESC'], // Piloter l'ordre
    operations: [
        new GetCollection(),
        new Get(
            requirements: ['id' => '\d+'],
            normalizationContext: ['groups' => ['read:comment', 'read:full:comment']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new MetadataPost(
            denormalizationContext: ['groups' => ['create:comment']],
            controller: CommentCreateController::class
        ),
        new Patch( // application/merge-patch+json
            requirements: ['id' => '\d+'],
            denormalizationContext: ['groups' => ['update:comment']],
            security: "is_granted('EDIT_COMMENT', object)" // Il faut avoir la permission d'éditer le commentaire et tu reçoit le commentaire en parms, on vas ensuite créer un voter, 'object' fais référence à l'entité qui est actuellement ciblé
        ),
        new Delete(
            requirements: ['id' => '\d+'],
            security: "is_granted('EDIT_COMMENT', object)"
        )
    ]
),
ApiFilter(SearchFilter::class, properties: ['post' => 'exact'])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:comment'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 4)]
    #[Groups(['read:comment', 'create:comment', 'update:comment'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:comment'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:comment'])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[
        Groups(['read:full:comment', 'read:comment', 'create:comment'])
    ]
    private ?Post $post = null;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}