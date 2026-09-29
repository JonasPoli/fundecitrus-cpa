<?php

namespace App\Entity;

use App\Repository\ResearchAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchAreaRepository::class)]
class ResearchArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEn = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    /** @var Collection<int, ResearchLine> */
    #[ORM\OneToMany(targetEntity: ResearchLine::class, mappedBy: 'area')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->namePt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamePt(): ?string
    {
        return $this->namePt;
    }

    public function setNamePt(string $namePt): static
    {
        $this->namePt = $namePt;
        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;
        return $this;
    }

    public function getName(string $locale): ?string
    {
        return $locale === 'en' ? $this->nameEn : $this->namePt;
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

    /** @return Collection<int, ResearchLine> */
    public function getLines(): Collection
    {
        return $this->lines;
    }
}
