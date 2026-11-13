<?php

namespace App\Repository;

use App\Entity\ProjectDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectDocument>
 */
class ProjectDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectDocument::class);
    }

    /**
     * @return ProjectDocument[]
     */
    public function findForPage(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.project', 'p')->addSelect('p')
            ->leftJoin('p.pesquisador', 'r')->addSelect('r')
            ->leftJoin('p.pesquisadores', 'pr')->addSelect('pr')
            ->orderBy('d.year', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
