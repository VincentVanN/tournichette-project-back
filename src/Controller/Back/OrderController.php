<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    // /**
    //  * @Route("/new", name="app_back_order_new", methods={"GET", "POST"})
    //  */
    // public function new(Request $request, OrderRepository $orderRepository): Response
    // {
    //     $order = new Order();
    //     $form = $this->createForm(Order1Type::class, $order);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $orderRepository->add($order, true);

    //         return $this->redirectToRoute('app_back_order_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('back/order/new.html.twig', [
    //         'order' => $order,
    //         'form' => $form,
    //     ]);
    // }

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
     * @Route("/validate/{id}", name="_validate", methods={"GET"})
     */
    public function orderValidate(Order $order, OrderRepository $orderRepository): Response
    {
        $order->setPaymentStatus('yes');
        // $order->setDeliverStatus('yes');
        $orderRepository->add($order, true);


        return $this->redirectToRoute('app_back_order_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/validate/{id}", name="_validate", methods={"GET"})
     */
    public function orderDeliverer(Order $order, OrderRepository $orderRepository): Response
    {
        //$order->setPaymentStatus('yes');
         $order->setDeliverStatus('yes');
        $orderRepository->add($order, true);


        return $this->redirectToRoute('app_back_order_list', [], Response::HTTP_SEE_OTHER);
    }
}


// /**
//  * @Route("/{id}/edit", name="app_back_order_edit", methods={"GET", "POST"})
//  */
// public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
// {
    //     $form = $this->createForm(Order1Type::class, $order);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $orderRepository->add($order, true);

    //         return $this->redirectToRoute('app_back_order_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('back/order/edit.html.twig', [
    //         'order' => $order,
    //         'form' => $form,
    //     ]);
// }

// /**
//  * @Route("/{id}", name="app_back_order_delete", methods={"POST"})
//  */
// public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
// {
    //     if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
    //         $orderRepository->remove($order, true);
    //     }

    //     return $this->redirectToRoute('app_back_order_index', [], Response::HTTP_SEE_OTHER);
// }
