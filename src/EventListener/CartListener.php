<?php
namespace App\EventListener;

use App\Entity\Cart;
use App\Utils\MySlugger;
use Doctrine\ORM\Event\PreFlushEventArgs;

class CartListener
{
    private $slugger;

    public function __construct(MySlugger $mySlugger)
    {
        $this->slugger = $mySlugger;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function slugifyCartName(Cart $cart, PreFlushEventArgs $event): void
    {
        $cart->setSlug($this->slugger->slugify($cart->getName()));
    }
}