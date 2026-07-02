<?php

namespace App\Controller\Admin;

use App\Entity\Researcher;
use App\Form\ResearcherType;
use App\Repository\ResearcherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResearcherController extends AbstractController
{
    #[Route('/admin/researcher', name: 'app_admin_researcher_index', methods: ['GET'])]
    public function index(ResearcherRepository $researcherRepository): Response
    {
        return $this->render('admin/researcher/index.html.twig', [
            'researchers' => $researcherRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/admin/researcher/new', name: 'app_admin_researcher_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $researcher = new Researcher();
        $form = $this->createForm(ResearcherType::class, $researcher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($researcher);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_researcher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/researcher/new.html.twig', [
            'researcher' => $researcher,
            'form' => $form,
        ]);
    }

    #[Route('/admin/researcher/{id}/edit', name: 'app_admin_researcher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] Researcher $researcher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResearcherType::class, $researcher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_researcher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/researcher/edit.html.twig', [
            'researcher' => $researcher,
            'form' => $form,
        ]);
    }

    #[Route('/admin/researcher/{id}', name: 'app_admin_researcher_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Researcher $researcher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$researcher->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($researcher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_researcher_index', [], Response::HTTP_SEE_OTHER);
    }
}
