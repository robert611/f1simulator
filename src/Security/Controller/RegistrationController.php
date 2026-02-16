<?php

namespace Security\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Security\Entity\User;
use Security\Event\UserRegisteredEvent;
use Security\Form\RegistrationFormType;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RegistrationController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData(),
                ),
            );
            $user->setIsVerified(false);
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // do anything else you need here, like send an email
            $session = new Session();
            $session->getFlashBag()->add('auth_success', 'Rejestracja przebiegła pomyślnie');

            $this->eventDispatcher->dispatch(new UserRegisteredEvent($user));

            return $this->redirectToRoute('app_register');
        }

        return $this->render('@security/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
