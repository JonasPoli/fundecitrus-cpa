<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ReorderController extends AbstractController
{
    #[Route('/admin/reorder/{entityName}', name: 'app_admin_reorder', methods: ['POST'])]
    public function reorder(
        string $entityName,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $ids = json_decode($request->getContent(), true)['ids'] ?? [];
        
        $fqcn = 'App\\Entity\\' . $entityName;
        if (!class_exists($fqcn)) {
            return new JsonResponse(['error' => 'Entity not found'], 404);
        }
        
        $repo = $em->getRepository($fqcn);
        
        foreach ($ids as $pos => $id) {
            $item = $repo->find($id);
            if ($item && method_exists($item, 'setPosition')) {
                $item->setPosition($pos + 1);
            }
        }
        
        $em->flush();
        
        return new JsonResponse(['ok' => true]);
    }
}
