<?php

declare(strict_types=1);

namespace Admin\Form;

use Domain\Contract\DTO\DriverDTO;

final class DriverFormModel
{
    public string $name;
    public string $surname;
    public int $carNumber;
    public int $teamId;

    public static function fromDriver(DriverDTO $driver): self
    {
        $model = new self();
        $model->name = $driver->getName();
        $model->surname = $driver->getSurname();
        $model->carNumber = $driver->getCarNumber();
        $model->teamId = $driver->getTeam()->getId();

        return $model;
    }
}
