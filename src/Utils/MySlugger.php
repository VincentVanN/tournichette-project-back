<?php

namespace App\Utils;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger 
{
    private $slugger;

    public function __construct( SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
    * Slugify a string
    *
    * @param string $toBeSlugified
    * @return string
    */
    function slugify(string $toBeSlugified) :string
    {
        $mySlug = $this->slugger->slug($toBeSlugified)->lower();
        
        return $mySlug;
    }

    
}