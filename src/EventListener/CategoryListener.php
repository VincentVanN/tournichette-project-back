<?php
namespace App\EventListener;

use App\Entity\Category;
use App\Utils\MySlugger;
use Doctrine\ORM\Event\PreFlushEventArgs;

class CategoryListener
{
    private $slugger;

    public function __construct(MySlugger $mySlugger)
    {
        $this->slugger = $mySlugger;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function slugifyCategoryName(Category $category, PreFlushEventArgs $event): void
    {
        $category->setSlug($this->slugger->slugify($category->getName()));
    }
}