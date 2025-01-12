<?php

declare(strict_types=1);

namespace App\Model;

class DriverPodiums
{
    private int $firstPlacePodiums = 0;
    private int $secondPlacePodiums = 0;
    private int $thirdPlacePodiums = 0;

    public function getFirstPlacePodiums(): int
    {
        return $this->firstPlacePodiums;
    }

    public function getThirdPlacePodiums(): int
    {
        return $this->thirdPlacePodiums;
    }

    public function getSecondPlacePodiums(): int
    {
        return $this->secondPlacePodiums;
    }

    public static function create(
        int $firstPlacePodiums,
        int $thirdPlacePodiums,
        int $secondPlacePodiums,
    ): self {
        $driverPodiums = new self();
        $driverPodiums->firstPlacePodiums = $firstPlacePodiums;
        $driverPodiums->secondPlacePodiums = $thirdPlacePodiums;
        $driverPodiums->thirdPlacePodiums = $secondPlacePodiums;

        return $driverPodiums;
    }
}
