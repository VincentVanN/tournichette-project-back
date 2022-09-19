<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Form\OrderType;
use App\Utils\PdfService;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back/orders", name="app_back_order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="_list", methods={"GET"})
     */
    public function list(OrderRepository $orderRepository): Response
    {
        return $this->render('back/order/list.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('back/order/show.html.twig', [
            'order' => $order,
        ]);
    }

     /**
     * @Route("/stats", name="_stats", methods={"GET"})
     */
    public function orderStats(Order $order) : Response
    {   
        return $this->render('back/order/stats.html.twig', [
            'order' => $order
            ] );
    }
    
    /**
     * @Route("/pdf/{id}", name="_detail.pdf", methods={"GET"})
     */
    public function generatePdfOrder(Order $order, PdfService $pdf, $id) 
    {   
        //dump($order);
        $html = $this->render('back/order/detail.html.twig', ['order' => $order] );
        $pdf->showPdfFile($html);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/validate/{id}", name="_validate", methods={"GET"})
     */
    public function orderValidate(Order $order, OrderRepository $orderRepository, $id): Response
    {
        $order->setPaymentStatus('yes');
        $orderRepository->add($order, true);
        //return $this->redirectToRoute('app_back_order_list', [], Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('app_back_order_list', ['_fragment' => $order->getId()]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/delivered/{id}", name="_delivered", methods={"GET"})
     */
    public function orderDelivered(Order $order, OrderRepository $orderRepository): Response
    {
        //$order->setPaymentStatus('yes');
        $order->setDeliverStatus('yes');
        $orderRepository->add($order, true);

        return $this->redirectToRoute('app_back_order_list', ['_fragment' => $order->getId()]);
    }

    /**
     * @Route("/validate/{id<\d+>}", name="_validate-ajax", methods={"POST"})
     */
    public function orderValidateAjax(Order $order, OrderRepository $orderRepository, $id): Response
    {
        if($order !== null) {
            $order->setPaymentStatus('yes');
            $orderRepository->add($order, true);
            $data['orderId'] = $order->getId();
            $data['paidAt'] = $order->getPaidAt();
            
            return $this->json($data, Response::HTTP_OK);
        }
    }

     /**
     * @Route("/delivered/{id<\d+>}", name="_delivered-ajax", methods={"POST"})
     */
    public function orderDeliveredAjax(Order $order, OrderRepository $orderRepository): Response
    {
        if($order !== null) {
            $order->setDeliverStatus('yes');
            $orderRepository->add($order, true);
            $data['orderId'] = $order->getId();
            $data['deliveredAt'] = $order->getDeliveredAt();

            return $this->json($data, Response::HTTP_OK);
        }

        return $this->redirectToRoute('app_back_order_list', ['_fragment' => $order->getId()]);
    }

    
}
