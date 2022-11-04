<?php

namespace App\Controller\Back;

use DateTimeImmutable;
use App\Repository\CartOrderRepository;
use App\Repository\SalesStatusRepository;
use App\Repository\OrderProductRepository;
use App\Utils\Pdf\PdfLarge;
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
     * @Route("", name="_home")
     */
    public function index(OrderProductRepository $orderProductRepository, CartOrderRepository $cartOrderRepository, SalesStatusRepository $salesStatusRepository, Request $request, PdfLarge $pdfLarge): Response
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
        $productPackArray = [];
        $depotPackArray = [];

        //---------------------------------------------------
        // Creation of arrays with products bought in detail
        //---------------------------------------------------

        foreach ($allProducts as $currentProduct) {
            
            $totalQuantity = $currentProduct->getTotalQuantity();
            $packQuantity = $currentProduct->getQuantity();

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

            // Creation of $productPackArray
            if (($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') && $currentProduct->getProduct()->getQuantityUnity() == 1) {
                continue;
            }

            if (!isset($productPackArray[$currentProduct->getProduct()->getName()])) {
                
                if ($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') {
                    $productPackArray[$currentProduct->getProduct()->getName()] = ['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity() => $packQuantity];
                } else {
                    $productPackArray[$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
                }
            } else {
                
                if ($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') {
                    if (isset($productPackArray[$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()])) {
                
                        $productPackArray[$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()] += $packQuantity;
                    
                    } else {
                    
                        $productPackArray[$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()] = $packQuantity;
                    
                    }
                } else {
                    if (isset($productPackArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
                    
                        $productPackArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
                    
                    } else {
                    
                        $productPackArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
                    
                    }
                }
            }

            // Creation of $depotPackArray

            if (!isset($depotPackArray[$currentProduct->getOrders()->getDepot()->getId()])) {
                
                if ($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') {
                    $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()] = [
                        $currentProduct->getOrders()->getDepot()->getName() => [
                            $currentProduct->getProduct()->getName() => ['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity() => $packQuantity]
                        ]
                ];
                } else {
                    $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()] = [
                        $currentProduct->getOrders()->getDepot()->getName() => [
                            $currentProduct->getProduct()->getName() => [$currentProduct->getProduct()->getUnity() => $totalQuantity]
                        ]
                ];
                }
   
            } else {
                
                if (isset($depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()])) {
                
                    if(!isset($depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()])) {
                        
                        if ($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') {
                            $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()] = ['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity() => $packQuantity];
                        } else {
                            $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
                        }
                    } else {
                        
                        if ($currentProduct->getProduct()->getUnity() === 'g' || $currentProduct->getProduct()->getUnity() === 'Kg') {
                            if (isset($depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()])) {
                
                                $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()] += $packQuantity;
                            } else {
                    
                                $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()]['lot de ' . $currentProduct->getProduct()->getQuantityUnity() . ' ' . $currentProduct->getProduct()->getUnity()] = $packQuantity;
                            }
                        } else {

                            if (isset($depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
                    
                                $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
                            } else {
                    
                                $depotPackArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
                            }
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

        //-----------------
        // PDG Generator
        //-----------------

        if ($request->query->get('generate-pdf') && $request->query->get('generate-pdf') == 'generate') {

            $pdfTwigView = $this->renderView('back/harvest/pdf.html.twig', [
                'products' => $request->query->get('sort') && $request->query->get('sort') === 'pack' ? $productPackArray : $productsArray,
                'depots' => $request->query->get('sort') && $request->query->get('sort') === 'pack' ? $depotPackArray : $depotArray,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            return $pdfLarge->showPdfFile($pdfTwigView, 'journal-de-recolte-du-' . date('d-m-Y'));
        }

        return $this->render('back/harvest/index.html.twig', [
            'products' => $productsArray,
            'depots' => $depotArray,
            'packProducts' => $productPackArray,
            'packDepots' => $depotPackArray,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
