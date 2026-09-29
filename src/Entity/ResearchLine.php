<?php

namespace App\Entity;

use App\Repository\ResearchLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchLineRepository::class)]
class ResearchLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ResearchArea $area = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    /** @var Collection<int, ResearchModule> */
    #[ORM\OneToMany(targetEntity: ResearchModule::class, mappedBy: 'line', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $modules;

    /** @var Collection<int, Project> */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'researchLine')]
    private Collection $projects;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->namePt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArea(): ?ResearchArea
    {
        return $this->area;
    }

    public function setArea(?ResearchArea $area): static
    {
        $this->area = $area;
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
        return $locale === 'en' ? $this->descriptionEn : $this->descriptionPt;
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

    /** @return Collection<int, ResearchModule> */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(ResearchModule $module): static
    {
        if (!$this->modules->contains($module)) {
            $module->setPosition($this->modules->count());
            $this->modules->add($module);
            $module->setLine($this);
        }

        return $this;
    }

    public function removeModule(ResearchModule $module): static
    {
        $this->modules->removeElement($module);

        return $this;
    }

    /** @return Collection<int, Project> */
    public function getProjects(): Collection
    {
        return $this->projects;
    }
}
