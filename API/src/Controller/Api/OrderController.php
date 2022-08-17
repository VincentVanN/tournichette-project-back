<?php

namespace App\Controller\Api;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/** @Route("/api/v1/orders", name="api_v1_orders" )
 * 
 */
class OrderController extends AbstractController
{

    /**
    * Add a cart 
    * @Route("/create", name="_create", methods="POST")
    * @return Response
    */
    public function create(OrderRepository $orderRepository) :Response
    {
        // TODO
        // At this time, no datas are saved in BDDn but whe return a 201 HTTP Response Code

        return $this->json('OK', Response::HTTP_CREATED);
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