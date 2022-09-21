<?php

namespace App\Utils;

use DateTime;

/**
 * This class create a custom token based on username, password and DateTime
 */
class TokenCreator
{
    private $tokenExpiredTime;

    public function __construct(int $tokenExpiredTime)
    {
        $this->tokenExpiredTime = $tokenExpiredTime;
    }
    
    public function create(string $username, string $hachedPassword)
    {
        $dateTime = new DateTime();
        $plainTextToken = $username . $hachedPassword . $dateTime->format('d-m-Y H:i:s');

        return hash('sha512', $plainTextToken);
    }
    
    public function getTokenExpiredTime()
    {
        return $this->tokenExpiredTime;
    }
}