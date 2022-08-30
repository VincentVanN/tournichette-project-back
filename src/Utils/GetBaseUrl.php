<?php

namespace App\Utils;

/**
 * Return the environnement variable with base url of the server (need for absolute images path)
 */
class GetBaseUrl
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the value of baseUrl
     */ 
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}