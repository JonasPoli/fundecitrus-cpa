<?php

namespace App\Repository;

use App\Entity\AboutSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AboutSection>
 */
class AboutSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AboutSection::class);
    }

    public function findCurrent(): ?AboutSection
    {
        return $this->findOneBy([], ['id' => 'ASC']);
    }
}
