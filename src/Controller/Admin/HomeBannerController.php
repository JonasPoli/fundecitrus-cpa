<?php

namespace App\Controller\Admin;

use App\Entity\HomeBanner;
use App\Form\HomeBannerType;
use App\Repository\HomeBannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeBannerController extends AbstractController
{
    #[Route('/admin/home/banner', name: 'app_admin_home_banner_index', methods: ['GET'])]
    public function index(HomeBannerRepository $homeBannerRepository): Response
    {
        return $this->render('admin/home_banner/index.html.twig', [
            'home_banners' => $homeBannerRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/admin/home/banner/new', name: 'app_admin_home_banner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $homeBanner = new HomeBanner();
        $form = $this->createForm(HomeBannerType::class, $homeBanner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($homeBanner);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_home_banner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/home_banner/new.html.twig', [
            'home_banner' => $homeBanner,
            'form' => $form,
        ]);
    }

    #[Route('/admin/home/banner/{id}/edit', name: 'app_admin_home_banner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] HomeBanner $homeBanner, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HomeBannerType::class, $homeBanner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_home_banner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/home_banner/edit.html.twig', [
            'home_banner' => $homeBanner,
            'form' => $form,
        ]);
    }

    #[Route('/admin/home/banner/{id}', name: 'app_admin_home_banner_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] HomeBanner $homeBanner, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$homeBanner->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($homeBanner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_home_banner_index', [], Response::HTTP_SEE_OTHER);
    }
}
