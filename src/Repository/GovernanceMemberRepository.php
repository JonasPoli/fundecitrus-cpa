<?php

namespace App\Repository;

use App\Entity\GovernanceMember;
use App\Enum\GovernanceGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GovernanceMember>
 */
class GovernanceMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GovernanceMember::class);
    }

    /** @return array<string, GovernanceMember[]> members indexed by GovernanceGroup value, every group present */
    public function findGrouped(): array
    {
        $grouped = array_fill_keys(array_column(GovernanceGroup::cases(), 'value'), []);

        $members = $this->createQueryBuilder('m')
            ->leftJoin('m.image', 'i')->addSelect('i')
            ->orderBy('m.position', 'ASC')
            ->addOrderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($members as $member) {
            $grouped[$member->getGovernanceGroup()->value][] = $member;
        }

        return $grouped;
    }
}
