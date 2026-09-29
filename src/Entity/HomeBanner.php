<?php

namespace App\Entity;

use App\Repository\HomeBannerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeBannerRepository::class)]
class HomeBanner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonTextPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonTextEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonLink = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $position = 0;

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

    public function getSubtitlePt(): ?string
    {
        return $this->subtitlePt;
    }

    public function setSubtitlePt(?string $subtitlePt): static
    {
        $this->subtitlePt = $subtitlePt;
        return $this;
    }

    public function getSubtitleEn(): ?string
    {
        return $this->subtitleEn;
    }

    public function setSubtitleEn(?string $subtitleEn): static
    {
        $this->subtitleEn = $subtitleEn;
        return $this;
    }

    public function getButtonTextPt(): ?string
    {
        return $this->buttonTextPt;
    }

    public function setButtonTextPt(?string $buttonTextPt): static
    {
        $this->buttonTextPt = $buttonTextPt;
        return $this;
    }

    public function getButtonTextEn(): ?string
    {
        return $this->buttonTextEn;
    }

    public function setButtonTextEn(?string $buttonTextEn): static
    {
        $this->buttonTextEn = $buttonTextEn;
        return $this;
    }

    public function getButtonLink(): ?string
    {
        return $this->buttonLink;
    }

    public function setButtonLink(?string $buttonLink): static
    {
        $this->buttonLink = $buttonLink;
        return $this;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }
}
