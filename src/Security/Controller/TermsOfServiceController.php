<?php

declare(strict_types=1);

namespace Security\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TermsOfServiceController extends AbstractController
{
    #[Route('/terms-of-service', name: 'app_terms_of_service')]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();

        if ($locale === 'en') {
            return $this->render('@security/registration/terms_of_service_en.html.twig');
        }

        return $this->render('@security/registration/terms_of_service_pl.html.twig');
    }
}
