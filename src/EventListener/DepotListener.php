<?php
namespace App\EventListener;

use App\Entity\Depot;
use App\Utils\MySlugger;
use Doctrine\ORM\Event\PreFlushEventArgs;

class DepotListener
{
    private $slugger;

    public function __construct(MySlugger $mySlugger)
    {
        $this->slugger = $mySlugger;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function slugifydepotName(Depot $depot, PreFlushEventArgs $event): void
    {
        $depot->setSlug($this->slugger->slugify($depot->getName()));
    }
}