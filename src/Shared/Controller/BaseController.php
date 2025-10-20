<?php

declare(strict_types=1);

namespace Shared\Controller;

use Security\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public function getUser(): ?User
    {
        /** @var User $user */
        $user = parent::getUser();

        return $user;
    }
}
