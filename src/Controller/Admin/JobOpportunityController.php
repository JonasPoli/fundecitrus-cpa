<?php

namespace App\Controller\Admin;

use App\Entity\JobOpportunity;
use App\Form\JobOpportunityType;
use App\Repository\JobOpportunityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class JobOpportunityController extends AbstractController
{
    #[Route('/admin/job/opportunity', name: 'app_admin_job_opportunity_index', methods: ['GET'])]
    public function index(JobOpportunityRepository $jobOpportunityRepository): Response
    {
        return $this->render('admin/job_opportunity/index.html.twig', [
            'job_opportunities' => $jobOpportunityRepository->findAll(),
        ]);
    }

    #[Route('/admin/job/opportunity/new', name: 'app_admin_job_opportunity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobOpportunity = new JobOpportunity();
        $form = $this->createForm(JobOpportunityType::class, $jobOpportunity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobOpportunity);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_job_opportunity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_opportunity/new.html.twig', [
            'job_opportunity' => $jobOpportunity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/job/opportunity/{id}/edit', name: 'app_admin_job_opportunity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] JobOpportunity $jobOpportunity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JobOpportunityType::class, $jobOpportunity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_job_opportunity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_opportunity/edit.html.twig', [
            'job_opportunity' => $jobOpportunity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/job/opportunity/{id}', name: 'app_admin_job_opportunity_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] JobOpportunity $jobOpportunity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobOpportunity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($jobOpportunity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_job_opportunity_index', [], Response::HTTP_SEE_OTHER);
    }
}
