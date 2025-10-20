<?php

namespace App\Entity;

use App\Repository\SalonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: SalonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Salon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom du salon est obligatoire.')]
    #[Assert\Length(max: 120, maxMessage: 'Le nom du salon ne doit pas dépasser {{ limit }} caractères.')]
    private string $name;

    #[ORM\Column(length: 140, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'L’adresse ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9]{4,10}$/', message: 'Le code postal doit contenir entre 4 et 10 chiffres.')]
    private ?string $postalCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(max: 120, maxMessage: 'Le nom de la ville ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $city = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9+\s().-]{6,}$/', message: 'Le numéro de téléphone n’est pas valide.')]
    private ?string $phone = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\Length(max: 150, maxMessage: 'L’adresse e-mail ne doit pas dépasser {{ limit }} caractères.')]
    #[Assert\Email(message: 'L’adresse e-mail "{{ value }}" n’est pas valide.', mode: 'strict')]
    #[Assert\Regex(pattern: '/^[^@\s]+@[^@\s]+\.[^@\s]+$/', message: 'Le format de l’adresse e-mail est invalide.')]
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull(message: 'La date de création est obligatoire.')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: 'La date de mise à jour est obligatoire.')]
    private \DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'salon', targetEntity: Artist::class, cascade: ['persist'])]
    private Collection $artists;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\OneToMany(targetEntity: Artist::class, mappedBy: 'Salon')]
    private Collection $artist;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
        $this->artist = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->updatedAt = new \DateTime();
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = strtolower((string) (new AsciiSlugger())->slug($this->name));
        }
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(?string $slug): self { $this->slug = $slug; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $postalCode): self { $this->postalCode = $postalCode; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): \DateTime { return $this->updatedAt; }
    public function setUpdatedAt(\DateTime $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    /** @return Collection<int, Artist> */
    public function getArtists(): Collection { return $this->artists; }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
            $artist->setSalon($this);
        }
        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->removeElement($artist)) {
            if ($artist->getSalon() === $this) {
                $artist->setSalon(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? ('Salon#' . $this->id);
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtist(): Collection
    {
        return $this->artist;
    }
}
