<?php

namespace App\Controller\Admin;

use App\Entity\ResearchArea;
use App\Form\ResearchAreaType;
use App\Repository\ResearchAreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResearchAreaController extends AbstractController
{
    #[Route('/admin/research-area', name: 'app_admin_research_area_index', methods: ['GET'])]
    public function index(ResearchAreaRepository $researchAreaRepository): Response
    {
        return $this->render('admin/research_area/index.html.twig', [
            'areas' => $researchAreaRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/admin/research-area/new', name: 'app_admin_research_area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $area = new ResearchArea();
        $form = $this->createForm(ResearchAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($area);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_research_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/research_area/form.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/admin/research-area/{id}/edit', name: 'app_admin_research_area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] ResearchArea $area, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResearchAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_research_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/research_area/form.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/admin/research-area/{id}', name: 'app_admin_research_area_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] ResearchArea $area, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $area->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($area);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_research_area_index', [], Response::HTTP_SEE_OTHER);
    }
}
