<?php

namespace App\Controller\Admin;

use App\Entity\GovernanceMember;
use App\Enum\GovernanceGroup;
use App\Form\GovernanceMemberType;
use App\Repository\GovernanceMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GovernanceMemberController extends AbstractController
{
    #[Route('/admin/governance', name: 'admin_governance_member_index', methods: ['GET'])]
    public function index(GovernanceMemberRepository $governanceMemberRepository): Response
    {
        return $this->render('admin/governance_member/index.html.twig', [
            'groups' => GovernanceGroup::cases(),
            'members' => $governanceMemberRepository->findGrouped(),
        ]);
    }

    #[Route('/admin/governance/new', name: 'admin_governance_member_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, GovernanceMemberRepository $governanceMemberRepository): Response
    {
        $member = new GovernanceMember();
        $member->setGovernanceGroup(GovernanceGroup::tryFrom($request->query->getString('group')));
        $form = $this->createForm(GovernanceMemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setPosition($governanceMemberRepository->count(['governanceGroup' => $member->getGovernanceGroup()]) + 1);
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('admin_governance_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/governance_member/form.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/admin/governance/{id}/edit', name: 'admin_governance_member_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] GovernanceMember $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GovernanceMemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_governance_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/governance_member/form.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/admin/governance/{id}', name: 'admin_governance_member_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] GovernanceMember $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $member->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_governance_member_index', [], Response::HTTP_SEE_OTHER);
    }
}
