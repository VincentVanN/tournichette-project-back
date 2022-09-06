<?php

namespace App\Controller\Api;

use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/** @Route("/api/v1/carts", name="api_v1_carts" )
 * 
 */
class CartController extends AbstractController
{

    /**
    * List all carts on sale
    * @Route("", name="_list", methods="GET")
    * @return Response
    */
    public function list(CartRepository $cartRepository) :Response
    {
        $cartsOnSale = $cartRepository->findBy(['onSale' => true]);

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_carts_list'],
            ['data' => $cartsOnSale]
        );
    }

    /**
     * Show one cart with given type
     * @Route("/{type}", name="_show", methods="GET")
     */
    public function show(string $type, CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['type_cart' => $type]);

        if ($cart === null)
        {
            return $this->prepareResponse(
                'Pas de panier trouvé avec ce slug',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_cart_show'],
            ['data' => $cart]
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