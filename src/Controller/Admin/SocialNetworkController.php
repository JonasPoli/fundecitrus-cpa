<?php

namespace App\Controller\Admin;

use App\Entity\SocialNetwork;
use App\Form\SocialNetworkType;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SocialNetworkController extends AbstractController
{
    #[Route('/admin/social-network', name: 'app_admin_social_network_index', methods: ['GET'])]
    public function index(SocialNetworkRepository $socialNetworkRepository): Response
    {
        return $this->render('admin/social_network/index.html.twig', [
            'social_networks' => $socialNetworkRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/admin/social-network/new', name: 'app_admin_social_network_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $socialNetwork = new SocialNetwork();
        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($socialNetwork);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_social_network_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/social_network/new.html.twig', [
            'social_network' => $socialNetwork,
            'form' => $form,
        ]);
    }

    #[Route('/admin/social-network/{id}/edit', name: 'app_admin_social_network_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] SocialNetwork $socialNetwork, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_social_network_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/social_network/edit.html.twig', [
            'social_network' => $socialNetwork,
            'form' => $form,
        ]);
    }

    #[Route('/admin/social-network/{id}', name: 'app_admin_social_network_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] SocialNetwork $socialNetwork, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$socialNetwork->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($socialNetwork);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_social_network_index', [], Response::HTTP_SEE_OTHER);
    }
}
