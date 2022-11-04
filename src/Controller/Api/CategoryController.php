<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/** @Route("/api/v1/categories", name="api_v1_categories" )
 * 
 */
class CategoryController extends AbstractController
{

    /**
    * List all categories 
    * @Route("", name="_list", methods="GET")
    * @return Response
    */
    public function list(CategoryRepository $categoryRepository) :Response
    {
        $allCategories = $categoryRepository->findAll();

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_categories_list'],
            ['data' => $allCategories]
        );
    }

    /**
     * List all products of given category
     *
     * @Route("/{slug}/products", name="_products", methods="GET")
     * @return Response
     */
    public function listProducts(string $slug, ProductRepository $productRepository) :Response
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

        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }
    
}