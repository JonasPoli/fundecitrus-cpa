<?php

namespace App\Controller\Admin;

use App\Entity\PageContent;
use App\Form\PageContentType;
use App\Repository\PageContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageContentController extends AbstractController
{
    #[Route('/admin/page/content', name: 'app_admin_page_content_index', methods: ['GET'])]
    public function index(PageContentRepository $pageContentRepository): Response
    {
        return $this->render('admin/page_content/index.html.twig', [
            'page_contents' => $pageContentRepository->findAll(),
        ]);
    }

    #[Route('/admin/page/content/new', name: 'app_admin_page_content_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pageContent = new PageContent();
        $form = $this->createForm(PageContentType::class, $pageContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pageContent);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_page_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page_content/new.html.twig', [
            'page_content' => $pageContent,
            'form' => $form,
        ]);
    }

    #[Route('/admin/page/content/{id}/edit', name: 'app_admin_page_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] PageContent $pageContent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PageContentType::class, $pageContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_page_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page_content/edit.html.twig', [
            'page_content' => $pageContent,
            'form' => $form,
        ]);
    }

    #[Route('/admin/page/content/{id}', name: 'app_admin_page_content_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] PageContent $pageContent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pageContent->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pageContent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_page_content_index', [], Response::HTTP_SEE_OTHER);
    }
}
