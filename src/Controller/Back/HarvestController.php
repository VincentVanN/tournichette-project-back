<?php

namespace App\Controller\Back;

use App\Entity\Order;
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
    public function index(OrderProductRepository $orderProductRepository, CartOrderRepository $cartOrderRepository): Response
    {

        // $allOrderProducts = $orderProductRepository->findAll();

        // $allProducts = $orderProductRepository->getTotalQuantityByProducts();
        // $allOrderProducts = $orderProductRepository->findAll();
        // $allProducts = $orderProductRepository->findAll();
        $allProducts = $orderProductRepository->findByDate();
        // dd($allProducts);
        $allCarts = $cartOrderRepository->findByDate();

        
        // foreach ($allDepots as $currentDepot) {
        //     dd($currentDepot->getOrders());
        // }
        $productsArray = [];
        $depotArray = [];

        foreach ($allProducts as $currentProduct) {
            // dd($currentProduct, $currentProduct->getTotalProducts());
            $totalQuantity = $currentProduct->getTotalQuantity();

            if ($currentProduct->getProduct()->getUnity() === 'g') {
                $totalQuantity = $totalQuantity / 1000;
                $currentProduct->getProduct()->setUnity('Kg');
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
        }

        $tempProductsArray = $productsArray;
        $tempDepotArray = $depotArray;

        foreach ($allCarts as $currentCart) {
            
            $allProductsInCurrentCart = $currentCart->getTotalProductsQuantity();
            $depotCurrentCartID = $currentCart->getOrders()->getDepot()->getId();
            $depotCurrentCartName = $currentCart->getOrders()->getDepot()->getName();
            // dd($currentCart->getTotalProductsQuantity());
            // dd($allProductsInCurrentCart);

            // Add to $productArray
            foreach ($allProductsInCurrentCart as $productName => $currentProduct) {
                // dd($currentProduct);
                if (!isset($productsArray[$productName])) {
                    $productsArray[$productName] = $currentProduct;
                } else {
                    foreach ($currentProduct as $unity => $totalQuantity) {
                        if (isset($productsArray[$productName][$unity])) {
                            $productsArray[$productName][$unity] += $totalQuantity;
                        } else {
                            $productsArray[$productName][$unity] = $totalQuantity;
                        }
                    }
                }
            }

            // Add to $depotArray

            foreach ($allProductsInCurrentCart as $productName => $currentProduct) {

                if (!isset($depotArray[$depotCurrentCartID])) {
                    $depotArray[$depotCurrentCartID] = [
                            $depotCurrentCartName => [$productName => $currentProduct]
                        ];
                } else {
                    if (isset($depotArray[$depotCurrentCartID][$depotCurrentCartName])) {
                        foreach ($currentProduct as $unity => $totalQuantity) {

                            if(!isset($depotArray[$depotCurrentCartID][$depotCurrentCartName][$productName])) {
                                $depotArray[$depotCurrentCartID][$depotCurrentCartName][$productName] = [$unity => $totalQuantity];
                            } else {
                                if (isset($depotArray[$depotCurrentCartID][$depotCurrentCartName][$productName][$unity])) {
                                    $depotArray[$depotCurrentCartID][$depotCurrentCartName][$productName][$unity] += $totalQuantity;
                                } else {
                                    $depotArray[$depotCurrentCartID][$depotCurrentCartName][$productName][$unity] = $totalQuantity;
                                }
                            }
                        
                        }
                    }
                }
            }

            
        }

        dd($tempProductsArray, $tempDepotArray, $productsArray, $depotArray);
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
