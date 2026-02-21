<?php

declare(strict_types=1);

namespace Security\EventListener;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Security\Entity\UserConfirmation;
use Security\Event\UserRegisteredEvent;
use Shared\Service\TokenGenerator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener]
readonly class UserRegisteredListener
{
    public function __construct(
        private MailerFacadeInterface $mailerFacade,
        private RouterInterface $router,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        $token = TokenGenerator::bin2hex(24);

        $verificationUrl = $this->router->generate(
            'app_confirm_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $userConfirmation = UserConfirmation::create(
            $user,
            $token,
            new DateTimeImmutable('+1 hour'),
        );

        $this->entityManager->persist($userConfirmation);
        $this->entityManager->flush();

        $this->mailerFacade->send(
            new GenericEmail(
                [$user->getEmail()],
                'Weryfikacja konta',
                '@mailer/account_confirmation/account_confirmation_email_pl.html.twig',
                '@mailer/account_confirmation/account_confirmation_email_pl.txt.twig',
                [
                    'username' => $user->getUsername(),
                    'verificationUrl' => $verificationUrl,
                ],
            ),
        );
    }
}
