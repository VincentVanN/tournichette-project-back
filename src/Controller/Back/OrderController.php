<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Utils\Pdf\PdfSticker;
use App\Repository\OrderRepository;
use DateTimeImmutable;
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
    public function list(OrderRepository $orderRepository, Request $request): Response
    {
        $orderBy = $request->query->get('order') ? $request->query->get('order') : 'ordered';
        $sort = $request->query->get('sort') ? $request->query->get('sort') : 'ASC';
        $startDate = null;
        $endDate = null;
        $orderedSort = ($orderBy === 'ordered' && $sort === 'ASC') ? 'DESC' : 'ASC';
        $userSort = ($orderBy === 'user' && $sort === 'ASC') ? 'DESC' : 'ASC';
        $paiementSort = ($orderBy === 'paiement' && $sort === 'ASC') ? 'DESC' : 'ASC';
        $deliveredSort = ($orderBy === 'delivered' && $sort === 'ASC') ? 'DESC' : 'ASC';

        if ($request->query->get('startDate')) {
            $startDate = new DateTimeImmutable($request->query->get('startDate'));
            $startDate = $startDate->format('Y-m-d');

            if ($request->query->get('endDate')) {
                $endDate = new DateTimeImmutable($request->query->get('endDate'));
                $endDate = $endDate->format('Y-m-d');
            }

            $orders = $orderRepository->findWithMultiFilters($startDate, $endDate, $orderBy, $sort);
        }

        if ($startDate === null) {
            $orders = $orderRepository->getSortedOrders($orderBy, $sort);
        }

        return $this->render('back/order/list.html.twig', [
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'orderedSort' => $orderedSort,
            'userSort' => $userSort,
            'paiementSort' => $paiementSort,
            'deliveredSort' => $deliveredSort
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('back/order/show.html.twig', [
            'order' => $order,
        ]);
    }
    
    /**
     * @Route("/pdf/{id<\d+>}", name="_detail.pdf", methods={"GET"})
     */
    public function generatePdfOrder(Order $order, PdfSticker $dompdf) 
    {   
        $html = $this->renderview('back/order/detail.html.twig', 
        ['order'=>$order,] 
    );
        $dompdf->showPdfFile($html);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/validate/{id<\d+>}", name="_validate", methods={"GET"})
     */
    public function orderValidate(Order $order, OrderRepository $orderRepository): Response
    {
        $order->setPaymentStatus('yes');
        $orderRepository->add($order, true);
        return $this->redirectToRoute('app_back_order_list', ['_fragment' => $order->getId()]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/delivered/{id<\d+>}", name="_delivered", methods={"GET"})
     */
    public function orderDelivered(Order $order, OrderRepository $orderRepository): Response
    {
        $order->setDeliverStatus('yes');
        $orderRepository->add($order, true);

        return $this->redirectToRoute('app_back_order_list', ['_fragment' => $order->getId()]);
    }

    /**
     * @Route("/validate/{id<\d+>}", name="_validate-ajax", methods={"POST"})
     */
    public function orderValidateAjax(Order $order, OrderRepository $orderRepository): Response
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
