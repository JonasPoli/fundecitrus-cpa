<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashController extends AbstractController
{
    #[Route('/', name: 'admin_dash')]
    public function dashboard(UserRepository $userRepository): Response
    {
        return $this->render('admin/dash/dashboard.html.twig', [
            'stats' => [
                'users_total' => $userRepository->count([]),
                'users_admin' => count(array_filter(
                    $userRepository->findAll(),
                    fn($u) => in_array('ROLE_ADMIN', $u->getRoles())
                )),
            ],
        ]);
    }
}
