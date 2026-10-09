<?php

namespace App\Repository;

use App\Entity\Researcher;
use App\Enum\ResearcherCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Researcher>
 */
class ResearcherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Researcher::class);
    }

    /**
     * Team grouped as group key (pesquisadores, bolsistas, apoio) => category value => researchers,
     * following the ResearcherCategory order and skipping empty categories.
     *
     * @return array<string, array<string, Researcher[]>>
     */
    public function findTeamGrouped(): array
    {
        $byCategory = [];
        $researchers = $this->createQueryBuilder('r')
            ->leftJoin('r.foto', 'f')->addSelect('f')
            ->orderBy('r.position', 'ASC')
            ->addOrderBy('r.nome', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($researchers as $researcher) {
            $byCategory[$researcher->getCategory()->value][] = $researcher;
        }

        $team = [];
        foreach (ResearcherCategory::cases() as $category) {
            if (isset($byCategory[$category->value])) {
                $team[$category->group()][$category->value] = $byCategory[$category->value];
            }
        }

        return $team;
    }
}
