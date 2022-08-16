<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/** @Route("/api/v1/products", name="api_v1_products" )
 * 
 */
class ProductController extends AbstractController
{

    /**
    * List all products 
    * @Route("", name="list", methods="GET")
    * @return Response
    */
    public function list(ProductRepository $productRepository) :Response
    {
        $allProducts = $productRepository->findAll();

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_products_list'],
            ['data' => $allProducts]
        );
    }

    /**
     * List all products of given category
     *
     * @Route("/{slug}/products", name="_products", methods="GET")
     * @return Response
     */
    public function listProducts(string $slug, CategoryRepository $categoryRepository, ProductRepository $productRepository) :Response
    {
        
        $products = $productRepository->findByCategory($slug);
        if ($products === null )
        {
            return $this->prepareResponse(
                'No products found for this category',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        // dd($products);
        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_category_product'],
            ['data' => $products]
        );
    }

    private function prepareResponse(
        string $message, 
        array $options = [], 
        array $data = [], 
        bool $isError = false, 
        int $httpCode = 200, 
        array $headers = []
    )
    {
        $responseData = [
            'error' => $isError,
            'message' => $message,
        ];

        // cf array_push qui fait se travaille merci @Steve A
        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }
    
}