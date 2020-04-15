<?php 

namespace App\Model;

class SecretGenerator 
{
    public function getSecret(): string
    {
        $alphabet = range('A', 'Z');

        $secret = null;

        for ($i = 1; $i <= 12; $i++) {
            $character = ceil(rand(0, 1));
            $secret .= $character == 1 ? $alphabet[rand(0, count($alphabet) - 1)] : ceil(rand(0, 8));
        }

        return $secret;
    }
}