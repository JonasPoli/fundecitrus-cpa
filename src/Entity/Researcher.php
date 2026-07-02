<?php

namespace App\Entity;

use App\Repository\ResearcherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $areaPt = null;

    #[ORM\Column(length: 255)]
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

    public function getAreaPt(): ?string
    {
        return $this->areaPt;
    }

    public function setAreaPt(string $areaPt): static
    {
        $this->areaPt = $areaPt;
        return $this;
    }

    public function getAreaEn(): ?string
    {
        return $this->areaEn;
    }

    public function setAreaEn(string $areaEn): static
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
}
