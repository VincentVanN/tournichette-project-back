<?php

namespace App\Utils;

/**
 * Return the environnement variable with base url of the server (need for absolute images path)
 */
class GetBaseUrl
{
    private $baseUrl;
    private $mailerUrl;
    private $mainUrl;

    public function __construct($baseUrl, $mailerUrl, $mainUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->mailerUrl = $mailerUrl;
        $this->mainUrl = $mainUrl;
    }

    /**
     * Get the value of baseUrl
     */ 
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Get the value of mailerUrl
     */ 
    public function getMailerUrl()
    {
        return $this->mailerUrl;
    }

    /**
     * Get the value of mainUrl
     */ 
    public function getMainUrl()
    {
        return $this->mainUrl;
    }
}