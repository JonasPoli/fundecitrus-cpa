<?php

namespace App\Entity;

use App\Repository\ResearchModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchModuleRepository::class)]
class ResearchModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ResearchLine $line = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEn = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    /** @var Collection<int, Project> */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'researchModule')]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function __toString(): string
    {
        return ($this->line ? $this->line->getNamePt() . ' › ' : '') . $this->namePt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLine(): ?ResearchLine
    {
        return $this->line;
    }

    public function setLine(?ResearchLine $line): static
    {
        $this->line = $line;
        return $this;
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

    /** @return Collection<int, Project> */
    public function getProjects(): Collection
    {
        return $this->projects;
    }
}
