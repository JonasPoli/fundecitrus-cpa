<?php

namespace App\Entity;

use App\Repository\NewsImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsImageRepository::class)]
class NewsImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gallery')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?News $news = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionEn = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): static
    {
        $this->news = $news;
        return $this;
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

    public function getCaptionPt(): ?string
    {
        return $this->captionPt;
    }

    public function setCaptionPt(?string $captionPt): static
    {
        $this->captionPt = $captionPt;
        return $this;
    }

    public function getCaptionEn(): ?string
    {
        return $this->captionEn;
    }

    public function setCaptionEn(?string $captionEn): static
    {
        $this->captionEn = $captionEn;
        return $this;
    }

    public function getCaption(string $locale): ?string
    {
        return $locale === 'en' ? ($this->captionEn ?: $this->captionPt) : $this->captionPt;
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
