<?php

namespace App\Controller\Admin;

use App\Entity\ResearchLine;
use App\Form\ResearchLineType;
use App\Repository\ResearchLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResearchLineController extends AbstractController
{
    #[Route('/admin/research-line', name: 'app_admin_research_line_index', methods: ['GET'])]
    public function index(ResearchLineRepository $researchLineRepository): Response
    {
        return $this->render('admin/research_line/index.html.twig', [
            'lines' => $researchLineRepository->findForPage(),
        ]);
    }

    #[Route('/admin/research-line/new', name: 'app_admin_research_line_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $line = new ResearchLine();
        $form = $this->createForm(ResearchLineType::class, $line);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($line);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_research_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/research_line/form.html.twig', [
            'line' => $line,
            'form' => $form,
        ]);
    }

    #[Route('/admin/research-line/{id}/edit', name: 'app_admin_research_line_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] ResearchLine $line, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResearchLineType::class, $line);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_research_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/research_line/form.html.twig', [
            'line' => $line,
            'form' => $form,
        ]);
    }

    #[Route('/admin/research-line/{id}', name: 'app_admin_research_line_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] ResearchLine $line, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $line->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($line);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_research_line_index', [], Response::HTTP_SEE_OTHER);
    }
}
