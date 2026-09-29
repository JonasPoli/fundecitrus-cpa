<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: ProjectDocument::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    #[ORM\Column(length: 255)]
    private ?string $nomePt = null;

    #[ORM\Column(length: 255)]
    private ?string $nomeEn = null;

    #[ORM\Column(length: 255)]
    private ?string $objetivoPt = null;

    #[ORM\Column(length: 255)]
    private ?string $objetivoEn = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descricaoPt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descricaoEn = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ResearchLine $researchLine = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ResearchModule $researchModule = null;

    #[ORM\Column(length: 255)]
    private ?string $slugPt = null;

    #[ORM\Column(length: 255)]
    private ?string $slugEn = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Researcher $pesquisador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomePt(): ?string
    {
        return $this->nomePt;
    }

    public function setNomePt(string $nomePt): static
    {
        $this->nomePt = $nomePt;
        return $this;
    }

    public function getNomeEn(): ?string
    {
        return $this->nomeEn;
    }

    public function setNomeEn(string $nomeEn): static
    {
        $this->nomeEn = $nomeEn;
        return $this;
    }

    public function getObjetivoPt(): ?string
    {
        return $this->objetivoPt;
    }

    public function setObjetivoPt(string $objetivoPt): static
    {
        $this->objetivoPt = $objetivoPt;
        return $this;
    }

    public function getObjetivoEn(): ?string
    {
        return $this->objetivoEn;
    }

    public function setObjetivoEn(string $objetivoEn): static
    {
        $this->objetivoEn = $objetivoEn;
        return $this;
    }

    public function getDescricaoPt(): ?string
    {
        return $this->descricaoPt;
    }

    public function setDescricaoPt(string $descricaoPt): static
    {
        $this->descricaoPt = $descricaoPt;
        return $this;
    }

    public function getDescricaoEn(): ?string
    {
        return $this->descricaoEn;
    }

    public function setDescricaoEn(string $descricaoEn): static
    {
        $this->descricaoEn = $descricaoEn;
        return $this;
    }

    public function getResearchLine(): ?ResearchLine
    {
        return $this->researchLine ?? $this->researchModule?->getLine();
    }

    public function setResearchLine(?ResearchLine $researchLine): static
    {
        $this->researchLine = $researchLine;
        return $this;
    }

    public function getResearchModule(): ?ResearchModule
    {
        return $this->researchModule;
    }

    public function setResearchModule(?ResearchModule $researchModule): static
    {
        $this->researchModule = $researchModule;
        if ($researchModule) {
            $this->researchLine = $researchModule->getLine();
        }
        return $this;
    }

    public function getModuleLabel(string $locale): ?string
    {
        if ($this->researchModule) {
            return $this->researchModule->getName($locale);
        }

        return $this->getResearchLine()?->getName($locale);
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

    public function getPesquisador(): ?Researcher
    {
        return $this->pesquisador;
    }

    public function setPesquisador(?Researcher $pesquisador): static
    {
        $this->pesquisador = $pesquisador;
        return $this;
    }

    /**
     * @return Collection<int, ProjectDocument>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(ProjectDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setProject($this);
        }

        return $this;
    }

    public function removeDocument(ProjectDocument $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getProject() === $this) {
                $document->setProject(null);
            }
        }

        return $this;
    }
}
