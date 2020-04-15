<?php 

namespace App\Model\Configuration;

class TeamsStrength
{
    public function getTeamsStrength(): array
    {
        return [
            'Mercedes' => 23,
            'Ferrari' => 19.7,
            'Red Bull' => 19.6,
            'Mclaren' => 6.4,
            'Renault' => 6.2,
            'Racing Point' => 6.1,
            'Toro Rosso' => 5.9,
            'Haas' => 5.7,
            'Alfa Romeo' => 5.7,
            'Williams' => 0.6
        ];
    }
}