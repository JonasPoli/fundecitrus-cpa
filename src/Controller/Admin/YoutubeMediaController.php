<?php

namespace App\Controller\Admin;

use App\Entity\YoutubeMedia;
use App\Form\YoutubeMediaType;
use App\Repository\YoutubeMediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/youtube-media')]
class YoutubeMediaController extends AbstractController
{
    #[Route('/', name: 'admin_youtube_media_index', methods: ['GET'])]
    public function index(YoutubeMediaRepository $repository): Response
    {
        return $this->render('admin/youtube_media/index.html.twig', [
            'medias' => $repository->findBy([], ['position' => 'ASC', 'id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_youtube_media_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $media = new YoutubeMedia();
        
        // Auto assign max position + 1
        $maxPos = $entityManager->createQuery('SELECT MAX(m.position) FROM App\Entity\YoutubeMedia m')->getSingleScalarResult();
        $media->setPosition(($maxPos ?? 0) + 1);

        $form = $this->createForm(YoutubeMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Mídia cadastrada com sucesso!');
            return $this->redirectToRoute('admin_youtube_media_index');
        }

        return $this->render('admin/youtube_media/new.html.twig', [
            'media' => $media,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_youtube_media_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, YoutubeMedia $media, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(YoutubeMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Mídia atualizada com sucesso!');
            return $this->redirectToRoute('admin_youtube_media_index');
        }

        return $this->render('admin/youtube_media/edit.html.twig', [
            'media' => $media,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_youtube_media_delete', methods: ['POST'])]
    public function delete(Request $request, YoutubeMedia $media, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $media->getId(), $request->request->get('_token'))) {
            $entityManager->remove($media);
            $entityManager->flush();
            $this->addFlash('success', 'Mídia excluída com sucesso!');
        }

        return $this->redirectToRoute('admin_youtube_media_index');
    }

}
