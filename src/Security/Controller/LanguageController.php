<?php

declare(strict_types=1);

namespace Security\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{locale}', name: 'app_change_language')]
    public function changeLanguage(string $locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_index');
    }
}
