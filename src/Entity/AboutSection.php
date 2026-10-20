<?php

namespace App\Entity;

use App\Repository\AboutSectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AboutSectionRepository::class)]
class AboutSection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $contentPt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $contentEn = null;

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

    public function setTitlePt(?string $titlePt): static
    {
        $this->titlePt = $titlePt;
        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->titleEn;
    }

    public function setTitleEn(?string $titleEn): static
    {
        $this->titleEn = $titleEn;
        return $this;
    }

    public function getTitle(string $locale): ?string
    {
        return $locale === 'en' ? $this->titleEn : $this->titlePt;
    }

    public function getContentPt(): ?string
    {
        return $this->contentPt;
    }

    public function setContentPt(?string $contentPt): static
    {
        $this->contentPt = $contentPt;
        return $this;
    }

    public function getContentEn(): ?string
    {
        return $this->contentEn;
    }

    public function setContentEn(?string $contentEn): static
    {
        $this->contentEn = $contentEn;
        return $this;
    }

    public function getContent(string $locale): ?string
    {
        return $locale === 'en' ? $this->contentEn : $this->contentPt;
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
}
