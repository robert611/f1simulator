<?php

declare(strict_types=1);

namespace Security\EventListener;

use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Security\Event\UserRegisteredEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class UserRegisteredListener
{
    public function __construct(
        private MailerFacadeInterface $mailerFacade,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        $this->mailerFacade->send(
            new GenericEmail(
                [$user->getEmail()],
                'Mail powitalny',
                '@mailer/welcome-email.html.twig',
                '@mailer/welcome-email.txt.twig',
                [
                    'username' => $user->getUsername(),
                    'homepageUrl' => $this->parameterBag->get('app_domain'),
                ],
            ),
        );
    }
}
