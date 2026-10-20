<?php

namespace App\Controller\Admin;

use App\Entity\AboutSection;
use App\Form\AboutSectionType;
use App\Repository\AboutSectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AboutSectionController extends AbstractController
{
    #[Route('/admin/quem-somos', name: 'admin_about_section_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AboutSectionRepository $aboutSectionRepository, EntityManagerInterface $entityManager): Response
    {
        $aboutSection = $aboutSectionRepository->findCurrent() ?? new AboutSection();
        $form = $this->createForm(AboutSectionType::class, $aboutSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aboutSection);
            $entityManager->flush();
            $this->addFlash('success', 'Seção "Quem somos" atualizada com sucesso.');

            return $this->redirectToRoute('admin_about_section_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/about_section/edit.html.twig', [
            'about_section' => $aboutSection,
            'form' => $form,
        ]);
    }
}
