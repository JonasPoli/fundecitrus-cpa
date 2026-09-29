<?php

namespace App\Repository;

use App\Entity\FooterCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FooterCategory>
 */
class FooterCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FooterCategory::class);
    }

    /** @return FooterCategory[] */
    public function findForFooter(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.companies', 'co')->addSelect('co')
            ->leftJoin('co.logo', 'l')->addSelect('l')
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('co.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
