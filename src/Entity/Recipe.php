<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Validator\BanWord;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute\Uploadable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute\UploadableField;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[UniqueEntity('title')] // Permet de dire que champ titre est unique
#[UniqueEntity('slug')]
#[Uploadable()]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column] # type: 'integer', options: ['unsigned' => true] Empêchant ainsi les valeurs négatives
    # Il faut aussi l’appliquer correctement dans les relations (ManyToOne, OneToMany, etc.), notamment pour les clés étrangères
    #[Groups(['recipes.index'])] # Ce champ correspond a ce groupe pour la partie Api
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, groups: ['Extra'])]
    #[BanWord()]
    #[Groups(['recipes.index', 'recipes.new', 'recipes.update'])]
    private string $title = '';

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, groups: ['Extra'])]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Slug Invalide")]
    #[Groups(['recipes.index', 'recipes.new'])]
    private string $slug = '';

    #[ORM\Column(type: Types::TEXT)] // Taille lenght: LONGTEXT
    #[Assert\Length(min: 5)]
    #[Groups(['recipes.new'])]
    private string $content = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank()] // On n'accepte pas les valeurs null, si on veut que la recette soit posistive
    #[Assert\Positive()] // Cette contrainte n'agit que si la valeur est un entier
    #[Assert\LessThan(value: 1440)] // 24 * 60 pour moins de 24 heures
    # #[Assert\CssColor()] -- Pour les champs de type couleurs
    #[Groups(['recipes.index', 'recipes.new'])]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[UploadableField(mapping: 'recipes', fileNameProperty: 'thumbnail', /*size: "imageSize"*/)] // Il utilise le mapping recipes dans notre fichier vich_uploader.yaml et la propriété thumbnail
    #[Assert\Image()] // On peut modifier la taille de l'image dans le constructeur
    private ?File $thumbnailFile = null;

    #[ORM\ManyToOne(inversedBy: 'recipes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")] ## En cas de suppression d'un utilisateur
    private ?User $user = null;

    // Je veux que ne cascade tu fasse de la persistance .. S'il reçoit un nouvel objet et que cette objet n'est pas persister, il le persistera de manière auto
    #[ORM\ManyToOne(inversedBy: 'recipes', cascade: ['persist'])]
    # #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false, options: ['unsigned' => true])] Clé étrangère category_id est UNSIGNED
    #[Groups(['recipes.show'])]
    private ?Category $category = null;

    /**
     * @var Collection<int, Quantity>
     */
    #[ORM\OneToMany(targetEntity: Quantity::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist'])] // Persist en cascade, rajouté par moi
    #[Assert\Valid()] // Automatiquement ça va valider que l'ensemble des quantités sont des éléments valides
    private Collection $quantities;

    /**
     * @var Collection<int, ImageCollection>
     */
    #[ORM\OneToMany(targetEntity: ImageCollection::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private Collection $imageCollections;

    public function __construct()
    {
        $this->quantities = new ArrayCollection();
        $this->imageCollections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): string
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnailFile(): ?File
    {
        return $this->thumbnailFile;
    }

    public function setThumbnailFile($thumbnailFile): static
    {
        $this->thumbnailFile = $thumbnailFile;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Quantity>
     */
    public function getQuantities(): Collection
    {
        return $this->quantities;
    }

    public function addQuantity(Quantity $quantity): static
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities->add($quantity);
            $quantity->setRecipe($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity): static
    {
        if ($this->quantities->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getRecipe() === $this) {
                $quantity->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImageCollection>
     */
    public function getImageCollections(): Collection
    {
        return $this->imageCollections;
    }

    public function addImageCollection(ImageCollection $imageCollection): static
    {
        if (!$this->imageCollections->contains($imageCollection)) {
            $this->imageCollections->add($imageCollection);
            $imageCollection->setRecipe($this);
        }

        return $this;
    }

    public function removeImageCollection(ImageCollection $imageCollection): static
    {
        if ($this->imageCollections->removeElement($imageCollection)) {
            // set the owning side to null (unless already changed)
            if ($imageCollection->getRecipe() === $this) {
                $imageCollection->setRecipe(null);
            }
        }

        return $this;
    }

}
