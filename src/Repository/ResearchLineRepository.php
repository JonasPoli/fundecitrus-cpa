<?php

namespace App\Repository;

use App\Entity\ResearchLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResearchLine>
 */
class ResearchLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResearchLine::class);
    }

    /** @return ResearchLine[] lines with image and modules loaded, in display order */
    public function findForPage(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.image', 'i')->addSelect('i')
            ->leftJoin('l.modules', 'm')->addSelect('m')
            ->orderBy('l.position', 'ASC')
            ->addOrderBy('l.id', 'ASC')
            ->addOrderBy('m.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
