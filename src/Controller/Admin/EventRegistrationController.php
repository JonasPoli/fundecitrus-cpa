<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\EventRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Storage\StorageInterface;

final class EventRegistrationController extends AbstractController
{
    #[Route('/admin/event/{id}/inscricoes', name: 'app_admin_event_registration_index', methods: ['GET'])]
    public function index(#[MapEntity(id: 'id')] Event $event): Response
    {
        return $this->render('admin/event_registration/index.html.twig', [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
        ]);
    }

    #[Route('/admin/event/{id}/inscricoes.csv', name: 'app_admin_event_registration_export', methods: ['GET'])]
    public function export(#[MapEntity(id: 'id')] Event $event): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($event) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Data', 'Nome', 'Instituição', 'Função', 'Cidade', 'E-mail', 'Telefone', 'Arquivo'], ';');
            foreach ($event->getRegistrations() as $registration) {
                fputcsv($handle, [
                    $registration->getCreatedAt()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('d/m/Y H:i'),
                    $registration->getName(),
                    $registration->getInstitution(),
                    $registration->getRole(),
                    $registration->getCity(),
                    $registration->getEmail(),
                    $registration->getPhone(),
                    $registration->getOriginalFileName() ?? '',
                ], ';');
            }
            fclose($handle);
        });

        $fileName = 'inscricoes-' . (new AsciiSlugger())->slug((string) $event->getTitlePt())->lower() . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName));

        return $response;
    }

    #[Route('/admin/inscricoes/{id}/arquivo', name: 'app_admin_event_registration_file', methods: ['GET'])]
    public function download(#[MapEntity(id: 'id')] EventRegistration $registration, StorageInterface $storage): BinaryFileResponse
    {
        $path = $storage->resolvePath($registration, 'file');
        if (!$path || !is_file($path)) {
            throw $this->createNotFoundException('Arquivo não encontrado.');
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $registration->getOriginalFileName() ?: $registration->getFileName(),
            (string) $registration->getFileName()
        );

        return $response;
    }

    #[Route('/admin/inscricoes/{id}/excluir', name: 'app_admin_event_registration_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] EventRegistration $registration, EntityManagerInterface $entityManager): Response
    {
        $eventId = $registration->getEvent()->getId();
        if ($this->isCsrfTokenValid('delete' . $registration->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($registration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_event_registration_index', ['id' => $eventId], Response::HTTP_SEE_OTHER);
    }
}
