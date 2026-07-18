<?php

declare(strict_types=1);

namespace Admin\Controller;

use Security\SecurityFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-user')]
class AdminUserController extends BaseController
{
    public function __construct(
        private readonly SecurityFacadeInterface $securityFacade,
    ) {
    }

    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->securityFacade->getUsers();

        return $this->render('@admin/admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $user = $this->securityFacade->getUserById($id);

        return $this->render('@admin/admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
