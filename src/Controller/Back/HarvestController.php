<?php

namespace App\Controller\Back;

use App\Repository\CartOrderRepository;
use App\Repository\CartProductRepository;
use App\Repository\DepotRepository;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
     * @Route("/back/harvest", name="app_back_harvest")
     */
class HarvestController extends AbstractController
{
    /**
     * @Route("/", name="_home")
     */
    public function index(OrderProductRepository $orderProductRepository, CartOrderRepository $cartOrderRepository ): Response
    {

        // $allOrderProducts = $orderProductRepository->findAll();

        // $allProducts = $orderProductRepository->getTotalQuantityByProducts();
        // $allOrderProducts = $orderProductRepository->findAll();
        $allProducts = $orderProductRepository->findAll();
        $allCarts = $cartOrderRepository->findAll();

        // dd($allCarts);


        // foreach ($allDepots as $currentDepot) {
        //     dd($currentDepot->getOrders());
        // }
        $productsArray = [];
        $depotArray = [];

        foreach ($allProducts as $currentProduct) {
            // dd($currentProduct, $currentProduct->getTotalProducts());
            $totalQuantity = $currentProduct->getTotalQuantity();

            if ($currentProduct->getProduct()->getUnity() === 'g') {
                $totalQuantity = $totalQuantity * 1000;
            }

            // Creation of $productArray

            if (!isset($productsArray[$currentProduct->getProduct()->getName()])) {
                $productsArray[$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
            } else {
                if (isset($productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
                    $productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
                } else {
                    $productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
                }
            }

            // Creation of $depotArray

            if (!isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()])) {
                $depotArray[$currentProduct->getOrders()->getDepot()->getId()] = [
                        $currentProduct->getOrders()->getDepot()->getName() => [
                            $currentProduct->getProduct()->getName() => [$currentProduct->getProduct()->getUnity() => $totalQuantity]
                    ]];
            } else {
                if (isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()])) {
                    if(!isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()])) {
                        $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
                    } else {
                        if (isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
                            $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
                        } else {
                            $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
                        }
                    }
                }
            }

            foreach ($allCarts as $currentCart) {
                $allCartProduct = $currentCart->getCart()->getCartProducts();
                $cartQuantity = $currentCart->getQuantity();
                foreach ($allCartProduct as $currentCartProduct) {
                    $productQuantity = $currentCartProduct->getTotalQuantity() * $cartQuantity;
                    // dd($productQuantity);
                }
            }

            // if (isset($productsArray[$currentProduct->getProduct()->getName()])) {
            //     $productsArray[$currentProduct->getProduct()->getName()]['total'] += $totalQuantity;
            // } else {
            //     $productsArray[$currentProduct->getProduct()->getName()]['total'] = $totalQuantity;
            // }

            // $orderProductsArray = [];
            // foreach ($currentProduct->getOrderProducts() as $currentOrderProduct) {
            //     // $orderProductsArray [] = $currentOrderProduct->getOrders()->getDepot()->getId();
            //     if (isset($depotsArray[$currentOrderProduct->getOrders()->getDepot()->getId()][$currentProduct->getId()])) {
            //         $depotsArray[$currentOrderProduct->getOrders()->getDepot()->getId()][$currentProduct->getId()]['total'] += $currentOrderProduct->getQuantity() + $currentProduct->getQuantityUnity();
            //     } else {
            //         $depotsArray[$currentOrderProduct->getOrders()->getDepot()->getId()]['depot'] = $currentOrderProduct->getOrders()->getDepot()->getName();
            //         $depotsArray[$currentOrderProduct->getOrders()->getDepot()->getId()][$currentProduct->getId()]['product'] = $currentProduct->getName();
            //         $depotsArray[$currentOrderProduct->getOrders()->getDepot()->getId()][$currentProduct->getId()]['total'] = $currentOrderProduct->getQuantity() + $currentProduct->getQuantityUnity();
            //     }
            // }
            // dd($orderProductsArray);

            // if (isset($depotsArray[$currentProduct->getOrderProducts()->getOrders()->getDepot()->getName()]))
        }

        // foreach ($allOrders as $currentOrder) {
        //     if (isset($depotsArray[$currentOrder->getDepot()->getId()][$currentOrder->getP])) {

        //     }
        // }

        dd($productsArray, $depotArray);

        // foreach ($allOrderProducts as $currentProduct) {
        //     // if ($currentProduct->getName() )
        //     // $currentProductArray =
        //     dd($currentProduct->getOrders(), $currentProduct->getProduct()->getName());
        // }

        dd($allProducts);

        return $this->render('back/harvest/index.html.twig', [
            'controller_name' => 'HarvestController',
        ]);
    }
}
