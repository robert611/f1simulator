<?php

declare(strict_types=1);

namespace Security\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Security\Entity\UserConfirmationToken;
use Security\Repository\UserConfirmationTokenRepository;
use Security\Repository\UserRepository;
use Shared\Service\TokenGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class UserConfirmationService
{
    public function __construct(
        private MailerFacadeInterface $mailerFacade,
        private RouterInterface $router,
        private UserConfirmationTokenRepository $userConfirmationTokenRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function sendConfirmationEmail(int $userId): void
    {
        $user = $this->userRepository->find($userId);
        $this->userConfirmationTokenRepository->invalidateUserTokens($user->getId());

        $token = TokenGenerator::bin2hex(24);

        $verificationUrl = $this->router->generate(
            'app_confirm_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $userConfirmationToken = UserConfirmationToken::create(
            $user,
            $token,
            new DateTimeImmutable('+1 hour'),
        );

        $this->entityManager->persist($userConfirmationToken);
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
                    'expiresInMinutes' => 60,
                ],
            ),
        );
    }

    public function isUserAllowedToResendEmail(int $userId): bool
    {
        $lastToken = $this->userConfirmationTokenRepository->findLastTokenForUser($userId);

        if (null !== $lastToken) {
            $oneMinuteAgo = new DateTimeImmutable('-1 minute');

            if ($lastToken->getCreatedAt() > $oneMinuteAgo) {
                return false;
            }
        }

        return true;
    }
}
