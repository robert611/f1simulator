<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Form\UserEditFormModel;
use Admin\Form\UserEditType;
use Security\SecurityFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->securityFacade->getUserById($id);

        $form = $this->createForm(UserEditType::class, UserEditFormModel::fromUser($user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserEditFormModel $userEditFormModel */
            $userEditFormModel = $form->getData();

            $this->securityFacade->updateUser($id, $userEditFormModel->isVerified, $userEditFormModel->isBlocked);
            $this->addFlash('admin_success', 'Użytkownik został zaktualizowany');

            return $this->redirectToRoute('admin_user_edit', ['id' => $id]);
        }

        return $this->render('@admin/admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
