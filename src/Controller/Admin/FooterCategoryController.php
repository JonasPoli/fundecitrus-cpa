<?php

namespace App\Controller\Admin;

use App\Entity\FooterCategory;
use App\Form\FooterCategoryType;
use App\Repository\FooterCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterCategoryController extends AbstractController
{
    #[Route('/admin/footer-category', name: 'app_admin_footer_category_index', methods: ['GET'])]
    public function index(FooterCategoryRepository $footerCategoryRepository): Response
    {
        return $this->render('admin/footer_category/index.html.twig', [
            'categories' => $footerCategoryRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/admin/footer-category/new', name: 'app_admin_footer_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new FooterCategory();
        $form = $this->createForm(FooterCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPosition($entityManager->getRepository(FooterCategory::class)->count([]) + 1);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_footer_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/footer_category/form.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/admin/footer-category/{id}/edit', name: 'app_admin_footer_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] FooterCategory $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FooterCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_footer_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/footer_category/form.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/admin/footer-category/{id}', name: 'app_admin_footer_category_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] FooterCategory $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_footer_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
