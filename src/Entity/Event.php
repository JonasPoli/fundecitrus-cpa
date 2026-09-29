<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentPt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentEn = null;

    #[ORM\Column(length: 255)]
    private ?string $datePt = null;

    #[ORM\Column(length: 255)]
    private ?string $dateEn = null;

    #[ORM\Column(length: 255)]
    private ?string $slugPt = null;

    #[ORM\Column(length: 255)]
    private ?string $slugEn = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitlePt(): ?string
    {
        return $this->titlePt;
    }

    public function setTitlePt(string $titlePt): static
    {
        $this->titlePt = $titlePt;
        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->titleEn;
    }

    public function setTitleEn(string $titleEn): static
    {
        $this->titleEn = $titleEn;
        return $this;
    }

    public function getContentPt(): ?string
    {
        return $this->contentPt;
    }

    public function setContentPt(string $contentPt): static
    {
        $this->contentPt = $contentPt;
        return $this;
    }

    public function getContentEn(): ?string
    {
        return $this->contentEn;
    }

    public function setContentEn(string $contentEn): static
    {
        $this->contentEn = $contentEn;
        return $this;
    }

    public function getDatePt(): ?string
    {
        return $this->datePt;
    }

    public function setDatePt(string $datePt): static
    {
        $this->datePt = $datePt;
        return $this;
    }

    public function getDateEn(): ?string
    {
        return $this->dateEn;
    }

    public function setDateEn(string $dateEn): static
    {
        $this->dateEn = $dateEn;
        return $this;
    }

    public function getSlugPt(): ?string
    {
        return $this->slugPt;
    }

    public function setSlugPt(string $slugPt): static
    {
        $this->slugPt = $slugPt;
        return $this;
    }

    public function getSlugEn(): ?string
    {
        return $this->slugEn;
    }

    public function setSlugEn(string $slugEn): static
    {
        $this->slugEn = $slugEn;
        return $this;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registrationLink = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $registrationOpen = false;

    /** @var Collection<int, EventRegistration> */
    #[ORM\OneToMany(targetEntity: EventRegistration::class, mappedBy: 'event')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function isRegistrationOpen(): bool
    {
        return $this->registrationOpen;
    }

    public function setRegistrationOpen(bool $registrationOpen): static
    {
        $this->registrationOpen = $registrationOpen;
        return $this;
    }

    /** @return Collection<int, EventRegistration> */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getRegistrationLink(): ?string
    {
        return $this->registrationLink;
    }

    public function setRegistrationLink(?string $registrationLink): static
    {
        $this->registrationLink = $registrationLink;
        return $this;
    }
}
