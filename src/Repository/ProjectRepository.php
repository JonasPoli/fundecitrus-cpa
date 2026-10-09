<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Projects with their responsible researcher, indexed by module id ('module') and,
     * for projects without a module, by line id ('line').
     *
     * @return array{module: array<int, Project[]>, line: array<int, Project[]>}
     */
    public function findGroupedByModuleAndLine(): array
    {
        $grouped = ['module' => [], 'line' => []];
        $projects = $this->createQueryBuilder('p')
            ->leftJoin('p.pesquisador', 'r')->addSelect('r')
            ->leftJoin('p.researchModule', 'm')->addSelect('m')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($projects as $project) {
            if ($project->getResearchModule()) {
                $grouped['module'][$project->getResearchModule()->getId()][] = $project;
            } elseif ($project->getResearchLine()) {
                $grouped['line'][$project->getResearchLine()->getId()][] = $project;
            }
        }

        return $grouped;
    }
}
