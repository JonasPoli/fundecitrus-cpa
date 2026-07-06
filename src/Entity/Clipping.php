<?php

namespace App\Entity;

use App\Repository\ClippingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClippingRepository::class)]
class Clipping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $veiculo = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVeiculo(): ?string
    {
        return $this->veiculo;
    }

    public function setVeiculo(string $veiculo): static
    {
        $this->veiculo = $veiculo;
        return $this;
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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summaryPt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summaryEn = null;

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;
        return $this;
    }

    public function getSummaryPt(): ?string
    {
        return $this->summaryPt;
    }

    public function setSummaryPt(?string $summaryPt): static
    {
        $this->summaryPt = $summaryPt;
        return $this;
    }

    public function getSummaryEn(): ?string
    {
        return $this->summaryEn;
    }

    public function setSummaryEn(?string $summaryEn): static
    {
        $this->summaryEn = $summaryEn;
        return $this;
    }
}
