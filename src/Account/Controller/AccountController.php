<?php

declare(strict_types=1);

namespace Account\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/index', name: 'account_index')]
    public function index(): Response
    {
        return $this->render('@account/index.html.twig');
    }
}
