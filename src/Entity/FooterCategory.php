<?php

namespace App\Entity;

use App\Repository\FooterCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FooterCategoryRepository::class)]
class FooterCategory
{
    public const LOGO_SIZES = [
        'Grande' => 'lg',
        'Médio' => 'md',
        'Pequeno' => 'sm',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $namePt = null;

    #[ORM\Column(length: 120)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 2, options: ['default' => 'md'])]
    private string $logoSize = 'md';

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    /** @var Collection<int, FooterCompany> */
    #[ORM\OneToMany(targetEntity: FooterCompany::class, mappedBy: 'category')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
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
        return $locale === 'en' ? ($this->nameEn ?: $this->namePt) : $this->namePt;
    }

    public function getLogoSize(): string
    {
        return $this->logoSize;
    }

    public function setLogoSize(string $logoSize): static
    {
        $this->logoSize = in_array($logoSize, self::LOGO_SIZES, true) ? $logoSize : 'md';
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

    /** @return Collection<int, FooterCompany> */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /** @return FooterCompany[] */
    public function getActiveCompanies(): array
    {
        return array_values($this->companies->filter(fn (FooterCompany $company) => $company->isActive())->toArray());
    }
}
