<?php

namespace App\Entity;

use App\Enum\GovernanceGroup;
use App\Repository\GovernanceMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GovernanceMemberRepository::class)]
class GovernanceMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, enumType: GovernanceGroup::class)]
    #[Assert\NotNull]
    private ?GovernanceGroup $governanceGroup = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGovernanceGroup(): ?GovernanceGroup
    {
        return $this->governanceGroup;
    }

    public function setGovernanceGroup(?GovernanceGroup $governanceGroup): static
    {
        $this->governanceGroup = $governanceGroup;
        return $this;
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
        return $locale === 'en' && $this->titleEn ? $this->titleEn : $this->titlePt;
    }

    public function getDescriptionPt(): ?string
    {
        return $this->descriptionPt;
    }

    public function setDescriptionPt(?string $descriptionPt): static
    {
        $this->descriptionPt = $descriptionPt;
        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): static
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

    public function getDescription(string $locale): ?string
    {
        return $locale === 'en' && $this->descriptionEn ? $this->descriptionEn : $this->descriptionPt;
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }
}
