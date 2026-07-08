<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[UniqueEntity('title')]
// #[Uploadable()]
class Property
{
    const HEAD = [
        0 => 'electric',
        1 => 'gaz',
        111 => 'OK'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 4)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 8)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Range(min:10, max:400)]
    private ?int $surface = null;

    #[ORM\Column]
    private ?int $rooms = null;

    #[ORM\Column]
    private ?int $bedrooms = null;

    #[ORM\Column]
    private ?int $floor = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column()]
    private ?int $heat = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $postal_code = null;

    #[ORM\Column(options:['default' => false])]
    private bool $sold = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Option>
     */
    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'properties')] ## mappedBy remplacé à cause du selected
    ## Ca va ensuite poser un problème avec le nom de la table intermédiare alors je dois faire un rollback
    private Collection $options;

    /* Pour le Picture
        #[ORM\Column(length: 255, nullable: true)]
        private ?string $image = null;

        #[UploadableField(mapping: 'properties', fileNameProperty: 'image')]
        #[Assert\Image(mimeTypes: ['image/jpeg'])] // Extends de File
        private ?File $imageFile = null;
    */

    #[Assert\All([
        new Image(mimeTypes: ['image/jpeg', 'image/png'])
    ])] // Permet de valider chaque élément de notre tableau
    private array $pictureFiles = []; // --

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'property', orphanRemoval: true, cascade: ['persist'])] // Va permettre de persister les 'Picture' car si on modifie seulement le champ image symfony ne détecte pas qu'il y'a eu un chamgement
    // Problème n+1 : - fetch: "EAGER" : Va faire la jointure à tout moment LEFT JOIN et va récupérer toutes les images associés à mes biens . 2 dans le Repository leftJoin
    private Collection $pictures;

    #[ORM\Column(
        nullable: true,
        scale: 4, // Lorsqu'on entre un 'float' dans une base de données on a besoin de préciser combien de chiffres il va pouvoir sauvegarder, 'scale' nombre de chiffre après la virgule
        precision: 6 // Le nombre de chiffre total, ça fais 4 chiffres plus 2 chiffres avant la virgule pour le cas de la 'latitude' qui vas entre '0' et '90'
    )]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude invalide')]
    private ?float $latitude = null;

    #[ORM\Column(
        nullable: true,
        scale: 4,
        precision: 7 // !! 3 chiffres avant la virgule pour le 'longitude' qui vas entre '-180' et '180'
    )]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude invalide')]
    private ?float $longitude = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->options = new ArrayCollection();
        $this->pictures = new ArrayCollection();
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

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getFormatedPrice(): string
    {
        return number_format($this->price, 0, '', ' ');
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getHeat(): ?int
    {
        return $this->heat;
    }

    public function setHeat(int $heat): static
    {
        $this->heat = $heat;

        return $this;
    }

    public function getHeatType(): string
    {
        return self::HEAD[$this->heat];
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function isSold(): bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): static
    {
        $this->sold = $sold;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    public function removeOption(Option $option): static
    {
        $this->options->removeElement($option);

        return $this;
    }

    /*
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

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        // Vich
        if($this->getImageFile() instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTimeImmutable);
        }

        return $this;
    }
    */

    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    public function getPicture(): ?Picture // Plutôt que de complexcifié ma vue, je crée ce getter
    {
        // if(!empty($this->pictures)) Ou vue que c'est une ArrayCollection
        if($this->pictures->isEmpty()) {
            return null;
        }
        // return $this->pictures[0];
        return $this->pictures->first();
    }

    public function setPictureFiles(array $pictureFiles): static
    {
        foreach($pictureFiles as $pictureFile) {
            if($pictureFile instanceof UploadedFile) { // --
                $picture = new Picture();
                $picture->setImageFile($pictureFile);
                $this->addPicture($picture);
            }
        }
        $this->pictureFiles = $pictureFiles; // On pourait le supprimer car ça ne sert pas à grand chose mais par rapport à la logique d'un setter on vas le garder
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProperty($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getProperty() === $this) {
                $picture->setProperty(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

}
