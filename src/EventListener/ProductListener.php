<?php
namespace App\EventListener;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Utils\MySlugger;
use Doctrine\ORM\Event\PreFlushEventArgs;

class ProductListener
{
    private $slugger;
    private $productRepository;

    public function __construct(MySlugger $mySlugger, ProductRepository $productRepository)
    {
        $this->slugger = $mySlugger;
        $this->productRepository = $productRepository;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function slugifyProductName(Product $product, PreFlushEventArgs $event): void
    {
        $stringToSlugify = $product->getName() . ' ' . $product->getQuantityUnity() . ' ' . $product->getUnity();

        $sameProductsName = $this->productRepository->findNbProducts($product->getName(), $product->getQuantityUnity(), $product->getUnity());

        // dd($sameProductsName);
        if ($sameProductsName['total'] > 0) {
            $stringToSlugify .= ' ' . ($sameProductsName['total'] + 1);
        }

        $product->setSlug($this->slugger->slugify($stringToSlugify));
    }
}