<?php

namespace App\Controller\Admin;

use App\Entity\ProjectDocument;
use App\Form\ProjectDocumentType;
use App\Repository\ProjectDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/project-document')]
class ProjectDocumentController extends AbstractController
{
    #[Route('/', name: 'admin_project_document_index', methods: ['GET'])]
    public function index(ProjectDocumentRepository $repository): Response
    {
        return $this->render('admin/project_document/index.html.twig', [
            'documents' => $repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_project_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new ProjectDocument();
        $form = $this->createForm(ProjectDocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($document);
            $entityManager->flush();

            $this->addFlash('success', 'Documento científico cadastrado com sucesso!');
            return $this->redirectToRoute('admin_project_document_index');
        }

        return $this->render('admin/project_document/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_project_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectDocument $document, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectDocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Documento científico atualizado com sucesso!');
            return $this->redirectToRoute('admin_project_document_index');
        }

        return $this->render('admin/project_document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_project_document_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectDocument $document, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $entityManager->remove($document);
            $entityManager->flush();
            $this->addFlash('success', 'Documento científico excluído com sucesso!');
        }

        return $this->redirectToRoute('admin_project_document_index');
    }
}
