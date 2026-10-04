<?php

declare(strict_types=1);

namespace Account\Controller;

use Account\Form\ChangeEmail\ChangeEmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account/change-email')]
class ChangeEmailController extends AbstractController
{
    #[Route('', name: 'account_email', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ChangeEmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO, handle submitted form
        }

        return $this->render('@account/change_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
