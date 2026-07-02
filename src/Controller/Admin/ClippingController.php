<?php

namespace App\Controller\Admin;

use App\Entity\Clipping;
use App\Form\ClippingType;
use App\Repository\ClippingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClippingController extends AbstractController
{
    #[Route('/admin/clipping', name: 'app_admin_clipping_index', methods: ['GET'])]
    public function index(ClippingRepository $clippingRepository): Response
    {
        return $this->render('admin/clipping/index.html.twig', [
            'clippings' => $clippingRepository->findAll(),
        ]);
    }

    #[Route('/admin/clipping/new', name: 'app_admin_clipping_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $clipping = new Clipping();
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($clipping);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_clipping_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/clipping/new.html.twig', [
            'clipping' => $clipping,
            'form' => $form,
        ]);
    }

    #[Route('/admin/clipping/{id}/edit', name: 'app_admin_clipping_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] Clipping $clipping, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_clipping_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/clipping/edit.html.twig', [
            'clipping' => $clipping,
            'form' => $form,
        ]);
    }

    #[Route('/admin/clipping/{id}', name: 'app_admin_clipping_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Clipping $clipping, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clipping->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($clipping);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_clipping_index', [], Response::HTTP_SEE_OTHER);
    }
}
