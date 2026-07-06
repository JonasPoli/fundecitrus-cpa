<?php

namespace App\Entity;

use App\Repository\JobOpportunityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: JobOpportunityRepository::class)]
#[Vich\Uploadable]
class JobOpportunity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255)]
    private ?string $statusPt = null;

    #[ORM\Column(length: 255)]
    private ?string $statusEn = null;

    #[ORM\Column(length: 255)]
    private ?string $summaryPt = null;

    #[ORM\Column(length: 255)]
    private ?string $summaryEn = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentPt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentEn = null;

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

    public function getStatusPt(): ?string
    {
        return $this->statusPt;
    }

    public function setStatusPt(string $statusPt): static
    {
        $this->statusPt = $statusPt;
        return $this;
    }

    public function getStatusEn(): ?string
    {
        return $this->statusEn;
    }

    public function setStatusEn(string $statusEn): static
    {
        $this->statusEn = $statusEn;
        return $this;
    }

    public function getSummaryPt(): ?string
    {
        return $this->summaryPt;
    }

    public function setSummaryPt(string $summaryPt): static
    {
        $this->summaryPt = $summaryPt;
        return $this;
    }

    public function getSummaryEn(): ?string
    {
        return $this->summaryEn;
    }

    public function setSummaryEn(string $summaryEn): static
    {
        $this->summaryEn = $summaryEn;
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

    #[Vich\UploadableField(mapping: 'documents', fileNameProperty: 'pdfName')]
    private ?File $pdfFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function setPdfFile(?File $pdfFile = null): void
    {
        $this->pdfFile = $pdfFile;

        if (null !== $pdfFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPdfFile(): ?File
    {
        return $this->pdfFile;
    }

    public function setPdfName(?string $pdfName): void
    {
        $this->pdfName = $pdfName;
    }

    public function getPdfName(): ?string
    {
        return $this->pdfName;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
