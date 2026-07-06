<?php

namespace App\Controller\Pub;

use App\Repository\ClippingRepository;
use App\Repository\DocumentRepository;
use App\Repository\EventRepository;
use App\Repository\HomeBannerRepository;
use App\Repository\JobOpportunityRepository;
use App\Repository\NewsRepository;
use App\Repository\PartnerRepository;
use App\Repository\PageContentRepository;
use App\Repository\ProjectRepository;
use App\Repository\ResearcherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ProjectDocument;
use App\Entity\YoutubeMedia;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home_slash')]
    public function homeSlash(): Response
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'pt']);
    }

    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'pt|en'])]
    public function home(
        HomeBannerRepository $bannerRepo,
        PartnerRepository $partnerRepo,
        NewsRepository $newsRepo
    ): Response {
        return $this->render('pub/main/home.html.twig', [
            'banners' => $bannerRepo->findBy(['isActive' => true], ['position' => 'ASC']),
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'news' => $newsRepo->findBy([], ['date' => 'DESC'], 3),
        ]);
    }

    #[Route('/{_locale}/sobre', name: 'app_sobre', requirements: ['_locale' => 'pt|en'])]
    public function sobre(PartnerRepository $partnerRepo): Response
    {
        return $this->render('pub/main/sobre.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'partnersBR' => $partnerRepo->findBy(['region' => 'BR'], ['position' => 'ASC']),
            'partnersINT' => $partnerRepo->findBy(['region' => 'INT'], ['position' => 'ASC']),
        ]);
    }

    #[Route('/{_locale}/pesquisa', name: 'app_pesquisa', requirements: ['_locale' => 'pt|en'])]
    public function pesquisa(
        Request $request,
        ResearcherRepository $researcherRepo,
        ProjectRepository $projectRepo,
        PartnerRepository $partnerRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $search = $request->query->get('search', '');
        $projetos = $projectRepo->findAll();
        $projectDocuments = $entityManager->getRepository(ProjectDocument::class)->findBy([], ['year' => 'DESC', 'id' => 'DESC']);

        return $this->render('pub/main/pesquisa.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'pesquisadores' => $researcherRepo->findBy([], ['position' => 'ASC']),
            'projetos' => $projetos,
            'projectDocuments' => $projectDocuments,
            'search' => $search,
        ]);
    }

    #[Route('/{_locale}/comunicacao', name: 'app_comunicacao', requirements: ['_locale' => 'pt|en'])]
    public function comunicacao(
        Request $request,
        NewsRepository $newsRepo,
        ClippingRepository $clippingRepo,
        PartnerRepository $partnerRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $search = $request->query->get('search');
        if ($search) {
            $noticias = $newsRepo->createQueryBuilder('n')
                ->where('n.titlePt LIKE :search OR n.titleEn LIKE :search OR n.summaryPt LIKE :search OR n.summaryEn LIKE :search OR n.contentPt LIKE :search OR n.contentEn LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('n.date', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $noticias = $newsRepo->findBy([], ['date' => 'DESC']);
        }

        $medias = $entityManager->getRepository(YoutubeMedia::class)->findBy([], ['position' => 'ASC', 'id' => 'DESC']);

        return $this->render('pub/main/comunicacao.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'noticias' => $noticias,
            'midia' => $clippingRepo->findAll(),
            'medias' => $medias,
            'search' => $search,
        ]);
    }

    #[Route('/{_locale}/eventos', name: 'app_eventos', requirements: ['_locale' => 'pt|en'])]
    public function eventos(EventRepository $eventRepo, PartnerRepository $partnerRepo, DocumentRepository $documentRepo): Response
    {
        $pastDocuments = $documentRepo->findBy(['folderPt' => 'Anais de Eventos'], ['createdAt' => 'DESC']);

        return $this->render('pub/main/eventos.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'agenda' => $eventRepo->findAll(),
            'pastDocuments' => $pastDocuments,
        ]);
    }

    #[Route('/{_locale}/oportunidades', name: 'app_oportunidades', requirements: ['_locale' => 'pt|en'])]
    public function oportunidades(JobOpportunityRepository $jobRepo, PartnerRepository $partnerRepo): Response
    {
        return $this->render('pub/main/oportunidades.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'vagas' => $jobRepo->findAll(),
        ]);
    }

    #[Route('/{_locale}/fale-conosco', name: 'app_fale_conosco', requirements: ['_locale' => 'pt|en'])]
    public function faleConosco(PartnerRepository $partnerRepo): Response
    {
        return $this->render('pub/main/fale_conosco.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/{_locale}/noticia/{slug}', name: 'app_noticia_detalhe', requirements: ['_locale' => 'pt|en'])]
    public function noticiaDetalhe(string $slug, NewsRepository $newsRepo, PartnerRepository $partnerRepo): Response
    {
        $item = $newsRepo->findOneBy(['slugPt' => $slug]) ?: $newsRepo->findOneBy(['slugEn' => $slug]);
        if (!$item) {
            throw $this->createNotFoundException('Notícia não encontrada');
        }

        $recentNews = $newsRepo->createQueryBuilder('n')
            ->where('n.id != :id')
            ->setParameter('id', $item->getId())
            ->orderBy('n.date', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $previousNews = $newsRepo->createQueryBuilder('n')
            ->where('n.date < :date')
            ->setParameter('date', $item->getDate())
            ->orderBy('n.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $nextNews = $newsRepo->createQueryBuilder('n')
            ->where('n.date > :date')
            ->setParameter('date', $item->getDate())
            ->orderBy('n.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('pub/main/detalhe/noticia.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
            'recentNews' => $recentNews,
            'previousNews' => $previousNews,
            'nextNews' => $nextNews,
        ]);
    }

    #[Route('/{_locale}/projeto/{slug}', name: 'app_projeto_detalhe', requirements: ['_locale' => 'pt|en'])]
    public function projetoDetalhe(string $slug, ProjectRepository $projectRepo, PartnerRepository $partnerRepo): Response
    {
        $item = $projectRepo->findOneBy(['slugPt' => $slug]) ?: $projectRepo->findOneBy(['slugEn' => $slug]);
        if (!$item) {
            throw $this->createNotFoundException('Projeto não encontrado');
        }

        return $this->render('pub/main/detalhe/projeto.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/evento/{slug}', name: 'app_evento_detalhe', requirements: ['_locale' => 'pt|en'])]
    public function eventoDetalhe(string $slug, EventRepository $eventRepo, PartnerRepository $partnerRepo): Response
    {
        $item = $eventRepo->findOneBy(['slugPt' => $slug]) ?: $eventRepo->findOneBy(['slugEn' => $slug]);
        if (!$item) {
            throw $this->createNotFoundException('Evento não encontrado');
        }

        return $this->render('pub/main/detalhe/evento.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/oportunidade/{slug}', name: 'app_oportunidade_detalhe', requirements: ['_locale' => 'pt|en'])]
    public function oportunidadeDetalhe(string $slug, JobOpportunityRepository $jobRepo, PartnerRepository $partnerRepo): Response
    {
        $item = $jobRepo->findOneBy(['slugPt' => $slug]) ?: $jobRepo->findOneBy(['slugEn' => $slug]);
        if (!$item) {
            throw $this->createNotFoundException('Oportunidade não encontrada');
        }

        return $this->render('pub/main/detalhe/oportunidade.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/pagina/{slug}', name: 'app_pagina_detalhe', requirements: ['_locale' => 'pt|en'])]
    public function paginaDetalhe(string $slug, PageContentRepository $pageContentRepository, PartnerRepository $partnerRepo): Response
    {
        $item = $pageContentRepository->findOneBy(['slugPt' => $slug, 'isActive' => true]) 
            ?: $pageContentRepository->findOneBy(['slugEn' => $slug, 'isActive' => true]);
            
        if (!$item) {
            throw $this->createNotFoundException('Página não encontrada');
        }

        return $this->render('pub/main/detalhe/pagina.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/pesquisador/{id}', name: 'app_pesquisador_detalhe', requirements: ['_locale' => 'pt|en', 'id' => '\d+'])]
    public function pesquisadorDetalhe(int $id, ResearcherRepository $researcherRepository, PartnerRepository $partnerRepo): Response
    {
        $item = $researcherRepository->find($id);
        if (!$item) {
            throw $this->createNotFoundException('Pesquisador não encontrado');
        }

        // Must have curriculum filled in either language to show detail page
        if (empty($item->getCurriculoPt()) && empty($item->getCurriculoEn())) {
            throw $this->createNotFoundException('Pesquisador sem currículo cadastrado');
        }

        return $this->render('pub/main/detalhe/pesquisador.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'item' => $item,
        ]);
    }

    #[Route('/{_locale}/area-restrita', name: 'app_area_restrita', requirements: ['_locale' => 'pt|en'])]
    public function areaRestrita(DocumentRepository $documentRepo, PartnerRepository $partnerRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $documents = $documentRepo->findBy([], ['createdAt' => 'DESC']);
        
        // Agrupar documentos por pasta
        $folders = [];
        foreach ($documents as $doc) {
            $folderPt = $doc->getFolderPt() ?: 'Outros';
            $folders[$folderPt][] = $doc;
        }

        return $this->render('pub/main/area_restrita.html.twig', [
            'partners' => $partnerRepo->findBy([], ['position' => 'ASC']),
            'folders' => $folders,
        ]);
    }

    #[Route('/{_locale}/contato', name: 'app_contact_post', methods: ['POST'], requirements: ['_locale' => 'pt|en'])]
    public function contactPost(
        Request $request,
        ParameterBagInterface $parameters,
        MailerInterface $mailer
    ): Response {
        $locale = $request->getLocale();
        try {
            $data = $request->request->all();
            
            $fromEmail = $parameters->has('emailFrom') ? $parameters->get('emailFrom') : 'noreply@fundecitrus.com.br';
            $toEmail = $parameters->has('emailContactTo') ? $parameters->get('emailContactTo') : 'contato@fundecitrus.com.br';

            $email = (new TemplatedEmail())
                ->from($fromEmail)
                ->to($toEmail)
                ->subject('Contato do site CPA Citros')
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                    'data' => $data,
                ])
            ;

            $mailer->send($email);
        } catch (\Exception $e) {
            $msg = $locale === 'en' ? 'There was an error sending your message.' : 'Houve um erro ao enviar seus dados.';
            $this->addFlash('contact_f', $msg);
            return $this->redirectToRoute('app_fale_conosco', ['_locale' => $locale]);
        }

        $msg = $locale === 'en' ? 'Your message has been sent successfully.' : 'Sua mensagem foi enviada com sucesso.';
        $this->addFlash('contact_s', $msg);
        return $this->redirectToRoute('app_fale_conosco', ['_locale' => $locale]);
    }
}
