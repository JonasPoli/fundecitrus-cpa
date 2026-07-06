<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\NewsRepository;
use App\Repository\ResearcherRepository;
use App\Repository\ProjectRepository;
use App\Repository\EventRepository;
use App\Repository\JobOpportunityRepository;
use App\Repository\DocumentRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashController extends AbstractController
{
    #[Route('/', name: 'admin_dash')]
    public function dashboard(
        UserRepository $userRepository,
        NewsRepository $newsRepo,
        ResearcherRepository $researcherRepo,
        ProjectRepository $projectRepo,
        EventRepository $eventRepo,
        JobOpportunityRepository $jobRepo,
        DocumentRepository $documentRepo,
        PartnerRepository $partnerRepo
    ): Response {
        $totalNews = $newsRepo->count([]);
        $totalResearchers = $researcherRepo->count([]);
        $totalProjects = $projectRepo->count([]);
        $totalEvents = $eventRepo->count([]);
        $totalJobs = $jobRepo->count([]);
        $totalDocuments = $documentRepo->count([]);
        $totalPartners = $partnerRepo->count([]);
        $openJobs = $jobRepo->count(['statusPt' => 'Aberto']);

        return $this->render('admin/dash/dashboard.html.twig', [
            'stats' => [
                'users_total' => $userRepository->count([]),
                'users_admin' => count(array_filter(
                    $userRepository->findAll(),
                    fn($u) => in_array('ROLE_ADMIN', $u->getRoles())
                )),
                'news_total' => $totalNews,
                'researchers_total' => $totalResearchers,
                'projects_total' => $totalProjects,
                'events_total' => $totalEvents,
                'jobs_total' => $totalJobs,
                'jobs_open' => $openJobs,
                'documents_total' => $totalDocuments,
                'partners_total' => $totalPartners,
            ],
        ]);
    }
}
