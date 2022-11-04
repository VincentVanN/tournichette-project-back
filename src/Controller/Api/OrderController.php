<?php

namespace App\Controller\Api;

use App\Entity\CartOrder;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\CartRepository;
use App\Repository\DepotRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
    public function create(
        Request $request,
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        DepotRepository $depotRepository,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        EntityManagerInterface $em,
        OrderRepository $orderRepository) :Response
    {
        $data = $request->getContent();
        $requestData = \json_decode($data, true);
        $priceOrder = 0;

        $order = $serializer->deserialize($data, Order::class, 'json');
        $order->resetOrderProducts();
        $order->resetCartOrders();
        $order->setUser($this->getUser());
        $order->setDeliverStatus('no');
        $order->setDateOrder(new \DateTimeImmutable());

        if (!isset($requestData['depot']) || $requestData['depot'] == '') {
            return $this->prepareResponse(
                'Le dépôt doit être sélectionné',
                [],
                [],
                true,
                Response::HTTP_BAD_REQUEST
            );
        }

        $depot = $depotRepository->find($requestData['depot']);

        if ($depot === null) {
            return $this->prepareResponse(
                'Aucun dépôt trouvé avec cet ID',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        $order->setDepot($depot);

        if (isset($requestData['orderProducts']) && count($requestData['orderProducts']) > 0) {

            foreach ($requestData['orderProducts'] as $currentOrderProduct)
            {
                if (isset($currentOrderProduct['quantity']) && $currentOrderProduct['quantity'] > 0) {
                    $orderProduct = $normalizer->denormalize($requestData['orderProducts'], OrderProduct::class, 'json');
                    $product = $productRepository->find($currentOrderProduct['id']);
                    
                    if ($product === null) {
                        return $this->prepareResponse(
                            'Aucun produit trouvé avec cet ID',
                            [],
                            [],
                            true,
                            Response::HTTP_NOT_FOUND
                        );
                    }

                    $orderProduct->setProduct($product);
                    $orderProduct->setQuantity($currentOrderProduct['quantity']);
                    $order->addOrderProduct($orderProduct);

                    $em->persist($orderProduct);

                    $priceOrder += $currentOrderProduct['quantity'] * $product->getPrice();
                }
            }
        }

        if (isset($requestData['cartOrders']) && count($requestData['cartOrders']) > 0) {

            foreach ($requestData['cartOrders'] as $currentCartOrder)
            {
                if (isset($currentCartOrder['quantity']) && $currentCartOrder['quantity'] >= 1) {
                    $cartOrder = $normalizer->denormalize($requestData['cartOrders'], CartOrder::class, 'json');
                    $cart = $cartRepository->find($currentCartOrder['id']);
                    
                    if ($cart === null) {
                        return $this->prepareResponse(
                            'Aucun panier trouvé avec cet ID',
                            [],
                            [],
                            true,
                            Response::HTTP_NOT_FOUND
                        );
                    }

                    $cartOrder->setCart($cart);
                    $cartOrder->setQuantity((int)$currentCartOrder['quantity']);
                    $order->addCartOrder($cartOrder);

                    $em->persist($cartOrder);

                    $priceOrder += $currentCartOrder['quantity'] * $cart->getPrice();
                }
            }
        }

        if (isset($requestData['paymentId'])) {
            $order->setPaymentStatus('yes');

            if (isset($requestData['stripeCustomerId'])) {
                $user = $this->getUser();
                $user->setStripeCustomerId($requestData['stripeCustomerId']);
            }
        } else {
            $order->setPaymentStatus('no');
        }

        $message = 'Order create.';

        if (($priceOrder != $order->getPrice()) && ($order->getPaymentStatus() != 'yes')) {
            $order->setPrice($priceOrder);
            $message .= ' The price has been adjusted.';
        }
        
        $em->persist($order);
        $em->flush();
        
        return $this->prepareResponse(
            $message,
            [],
            ['data' => $priceOrder],
            false,
            Response::HTTP_CREATED
        );
    }

    /**
    * Show an order of the user given by id
    * @Route("/{id}", name="_show", methods="GET", requirements={"id"="\d+"})
    * @return Response
    */
    public function show(int $id, OrderRepository $orderRepository) :Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->prepareResponse(
                'Utilisateur non connecté',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userOrder = $orderRepository->find($id);

        if ($userOrder === null) {
            return $this->prepareResponse(
                'Pas de commande trouvée avec cet ID',
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
                'Utilisateur non connecté',
                [],
                [],
                true,
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userOrders = $user->getOrders();

        if (count($userOrders) == 0) {
            return $this->prepareResponse(
                'Pas de commande trouvée pour cet utilisateur',
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