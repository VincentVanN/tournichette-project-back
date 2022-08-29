<?php

namespace App\Controller\Api;
use App\Repository\DepotRepository;
use App\Repository\ProductRepository;
use App\Utils\GetBaseUrl;
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
    * @Route("", name="_list", methods="GET")
    * @return Response
    */
    public function list(ProductRepository $productRepository, GetBaseUrl $baseUrl) :Response
    {
        $allProducts = $productRepository->findAll();

        foreach($allProducts as $currentProduct)
        {
            $currentProduct->setImage($baseUrl->getBaseUrl() . '/images/products/' . $currentProduct->getImage());
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_products_list'],
            ['data' => $allProducts]
        );
    }

    /**
     * Show one product with given slug
     * @Route("/{slug}", name="_show", methods="GET")
     */
    public function show(string $slug, ProductRepository $productRepository, GetBaseUrl $baseUrl): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if ($product === null)
        {
            return $this->prepareResponse(
                'No product found for this slug',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        $product->setImage($baseUrl->getBaseUrl() . '/images/products/' . $product->getImage());

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_product_show'],
            ['data' => $product]
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