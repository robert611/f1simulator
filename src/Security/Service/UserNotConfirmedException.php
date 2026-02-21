<?php

declare(strict_types=1);

namespace Security\Service;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserNotConfirmedException extends CustomUserMessageAccountStatusException
{
}
