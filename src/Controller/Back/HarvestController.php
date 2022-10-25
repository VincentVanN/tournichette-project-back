<?php

namespace App\Controller\Back;

use App\Entity\Order;
use DateTimeImmutable;
use App\Repository\DepotRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CartOrderRepository;
use App\Repository\CartProductRepository;
use App\Repository\SalesStatusRepository;
use App\Repository\OrderProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/back/harvest", name="app_back_harvest")
     */
class HarvestController extends AbstractController
{
    /**
     * @Route("/", name="_home")
     */
    public function index(OrderProductRepository $orderProductRepository, CartOrderRepository $cartOrderRepository, SalesStatusRepository $salesStatusRepository, Request $request): Response
    {   
        $salesStatus = $salesStatusRepository->findOneBy(['name' => 'status']);

        $startSales = $salesStatus->getStartAt();
        $endSales = $salesStatus->getEndAt(); 

        $startDate = $startSales;
        $endDate = $endSales;

        if ($request->query->get('startDate') && $request->query->get('endDate')) {
            $startDate = new DateTimeImmutable($request->query->get('startDate'));
            $endDate = new DateTimeImmutable($request->query->get('endDate'));
        }

        if ($request->query->get('startDate') && !$request->query->get('endDate')) {
            $startDate = new DateTimeImmutable($request->query->get('startDate'));
            $endDate = null;
        }

        if(!$request->query->get('startDate') && $request->query->get('endDate')) {
            $startDate = null;
            $endDate = new DateTimeImmutable($request->query->get('endDate'));
        }

        $startDate = $startDate !== null ? $startDate->format('Y-m-d') : $startDate;
        $endDate = $endDate !== null ? $endDate->format('Y-m-d') : $endDate;

        $allProducts = $orderProductRepository->findByDate($startDate, $endDate);
        $allCarts = $cartOrderRepository->findByDate($startDate, $endDate);

        $productsArray = [];
        $depotArray = [];

        //---------------------------------------------------
        // Creation of arrays with products bought in detail
        //---------------------------------------------------

        foreach ($allProducts as $currentProduct) {
            
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
                        ]
                ];
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

        //--------------------------------------------
        // Creation of arrays with products in carts
        //--------------------------------------------

        foreach ($allCarts as $currentCart) {
            
            $allProductsInCurrentCart = $currentCart->getTotalProductsQuantity();
            $depotCurrentCartID = $currentCart->getOrders()->getDepot()->getId();
            $depotCurrentCartName = $currentCart->getOrders()->getDepot()->getName();

            // Add to $productArray
            foreach ($allProductsInCurrentCart as $productName => $currentProduct) {

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

        // dd($productsArray, $depotArray);

        return $this->render('back/harvest/index.html.twig', [
            'products' => $productsArray,
            'depots' => $depotArray,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
