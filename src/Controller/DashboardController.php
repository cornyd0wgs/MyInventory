<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'username' => $user?->getUserIdentifier() ?? 'guest',
            'roles' => $user?->getRoles() ?? [],
        ]);
    }

    #[Route('/owner-only', name: 'app_owner_only')]
    #[IsGranted('ROLE_OWNER')]
    public function ownerOnly(): Response
    {
        return new Response('<p>Owner-only area — access granted.</p>');
    }
}
