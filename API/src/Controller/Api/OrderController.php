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
    * Add an order 
    * @Route("/create", name="_create", methods="POST")
    * @return Response
    */
    public function create(OrderRepository $orderRepository) :Response
    {
        // TODO
        // At this time, no datas are saved in BDD but whe return a 201 HTTP Response Code

        return $this->json('OK', Response::HTTP_CREATED);
    }

    /**
    * Show an order of the user given by id
    * @Route("/{id}", name="_show", methods="GET")
    * @return Response
    */
    public function show(int $id, OrderRepository $orderRepository) :Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->prepareResponse(
                'User not connected',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userOrder = $orderRepository->find($id);

        if ($userOrder === null) {
            return $this->prepareResponse(
                'No order found with this ID',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_order_user_show'],
            ['data' => $userOrder]
        );
    }


    /**
    * List all order of the user
    * @Route("/user", name="_user", methods="GET")
    * @return Response
    */
    public function list(OrderRepository $orderRepository) :Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->prepareResponse(
                'User not connected',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userOrders = $user->getOrders();

        if (count($userOrders) == 0) {
            return $this->prepareResponse(
                'No orders found for this user',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_orders_user'],
            ['data' => $userOrders]
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