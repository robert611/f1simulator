<?php

declare(strict_types=1);

namespace Mailer\AsyncCommand;

use Mailer\Service\MailerElasticLogger;
use Shared\Messenger\EmailQueue;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class SendEmailHandler implements EmailQueue
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private ParameterBagInterface $parameterBag,
        private MailerElasticLogger $mailerElasticLogger,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[AsMessageHandler(bus: 'messenger.bus.default')]
    public function __invoke(SendEmail $command): void
    {
        $htmlBody = $this->twig->render($command->htmlTemplate, $command->contentParams);
        $plainBody = $this->twig->render($command->plainTemplate, $command->contentParams);

        $email = (new Email())
            ->from($this->parameterBag->get('mailer_from_address'))
            ->to(...$command->to)
            ->subject($command->subject)
            ->html($htmlBody)
            ->text($plainBody);

        try {
            $this->mailer->send($email);
            $this->mailerElasticLogger->sent($command->to, $command->subject);
        } catch (TransportExceptionInterface $e) {
            $this->mailerElasticLogger->error($command->to, $command->subject, (string) $e);
        }
    }
}
