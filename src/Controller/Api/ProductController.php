<?php

namespace App\Controller\Api;
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
        $unarchivedProducts = $productRepository->findBy(['archived' => false], ['name' => 'ASC']);

        foreach($unarchivedProducts as $currentProduct)
        {
            if ($currentProduct->getImage() === null) {
                $currentProduct->setImage('placeholder.png');
            }

            $currentProduct->setImage($baseUrl->getBaseUrl() . '/images/products/' . $currentProduct->getImage());
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_products_list'],
            ['data' => $unarchivedProducts]
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
                'Pas de produit trouvé avec ce slug',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        if ($product->getImage() === null) {
            $product->setImage('placeholder.png');
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