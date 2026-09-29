<?php

namespace App\Controller\Admin;

use App\Entity\FooterCompany;
use App\Form\FooterCompanyType;
use App\Repository\FooterCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterCompanyController extends AbstractController
{
    #[Route('/admin/footer-company', name: 'app_admin_footer_company_index', methods: ['GET'])]
    public function index(FooterCategoryRepository $footerCategoryRepository): Response
    {
        return $this->render('admin/footer_company/index.html.twig', [
            'categories' => $footerCategoryRepository->findForFooter(),
        ]);
    }

    #[Route('/admin/footer-company/new', name: 'app_admin_footer_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FooterCategoryRepository $footerCategoryRepository): Response
    {
        $company = new FooterCompany();
        if ($request->query->getInt('category')) {
            $company->setCategory($footerCategoryRepository->find($request->query->getInt('category')));
        }
        $form = $this->createForm(FooterCompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company->setPosition($company->getCategory()->getCompanies()->count() + 1);
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_footer_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/footer_company/form.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/admin/footer-company/{id}/edit', name: 'app_admin_footer_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] FooterCompany $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FooterCompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_footer_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/footer_company/form.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/admin/footer-company/{id}', name: 'app_admin_footer_company_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] FooterCompany $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_footer_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
