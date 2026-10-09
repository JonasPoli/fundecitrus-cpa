<?php

namespace App\Entity;

use App\Enum\ResearcherCategory;
use App\Repository\ResearcherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearcherRepository::class)]
class Researcher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $institution = null;

    #[ORM\Column(length: 30, enumType: ResearcherCategory::class, options: ['default' => 'pesquisador'])]
    private ResearcherCategory $category = ResearcherCategory::RESEARCHER;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $areaPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $areaEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lattes = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $foto = null;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'pesquisador')]
    private Collection $projects;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $position = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $curriculoPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $curriculoEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;
        return $this;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(?string $institution): static
    {
        $this->institution = $institution;
        return $this;
    }

    public function getCategory(): ResearcherCategory
    {
        return $this->category;
    }

    public function setCategory(ResearcherCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getArea(string $locale): ?string
    {
        return $locale === 'en' && $this->areaEn ? $this->areaEn : $this->areaPt;
    }

    public function getInitials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->nome));
        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? end($parts) : '';

        return mb_strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
    }

    public function getAreaPt(): ?string
    {
        return $this->areaPt;
    }

    public function setAreaPt(?string $areaPt): static
    {
        $this->areaPt = $areaPt;
        return $this;
    }

    public function getAreaEn(): ?string
    {
        return $this->areaEn;
    }

    public function setAreaEn(?string $areaEn): static
    {
        $this->areaEn = $areaEn;
        return $this;
    }

    public function getLattes(): ?string
    {
        return $this->lattes;
    }

    public function setLattes(?string $lattes): static
    {
        $this->lattes = $lattes;
        return $this;
    }

    public function getFoto(): ?Image
    {
        return $this->foto;
    }

    public function setFoto(?Image $foto): static
    {
        $this->foto = $foto;
        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setPesquisador($this);
        }
        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            if ($project->getPesquisador() === $this) {
                $project->setPesquisador(null);
            }
        }
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

    public function getCurriculoPt(): ?string
    {
        return $this->curriculoPt;
    }

    public function setCurriculoPt(?string $curriculoPt): static
    {
        $this->curriculoPt = $curriculoPt;
        return $this;
    }

    public function getCurriculoEn(): ?string
    {
        return $this->curriculoEn;
    }

    public function setCurriculoEn(?string $curriculoEn): static
    {
        $this->curriculoEn = $curriculoEn;
        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }
}
