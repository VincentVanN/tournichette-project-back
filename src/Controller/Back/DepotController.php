<?php

namespace App\Controller\Back;

use App\Entity\Depot;
use DateTimeImmutable;
use App\Form\DepotType;
use App\Utils\Pdf\PdfLarge;
use App\Repository\DepotRepository;
use App\Repository\OrderRepository;
use App\Repository\SalesStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back/depot", name="app_back_depot")
 */
class DepotController extends AbstractController
{
    /**
    * List all depots 
    * @Route("", name="_list", methods="GET")
    * @return Response
    */
    public function list(DepotRepository $depotRepository) :Response
    {
        return $this->render('back/depot/index.html.twig',
            ['depots' => $depotRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DepotRepository $depotRepository): Response
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $depot->setAvailable(true);
            $depotRepository->add($depot, true);

            return $this->redirectToRoute('app_back_depot_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/depot/new.html.twig', [
            'depot' => $depot,
            'form' => $form,
        ]);
    }

    /**
     * 
     * @Route("/{id<\d+>}", name="_show", methods="GET")
     */
    public function show(Depot $depot, OrderRepository $orderRepository, Request $request): Response
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

            $orders = $orderRepository->findWithMultiFilters($startDate, $endDate, $orderBy, $sort, $depot);
        }

        if ($startDate === null) {
            $orders = $orderRepository->getSortedOrders($orderBy, $sort, $depot);
        }
        
        return $this->render('back/depot/show.html.twig', [
            'depot' => $depot,
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
     * @Route("/pdf/{id<\d+>}", name="_detail.pdf", methods={"GET"})
     */
    public function generatePdfDepot(Depot $depot, PdfLarge $dompdf, OrderRepository $orderRepository) 
    {   
        $orders = $orderRepository->findTotalPriceOrder($depot);
        $total=$orders['price'];
        $nborders=$orders['orders'];
        
        $html = $this->renderview('back/depot/detail.html.twig', 
        ['depot'=>$depot,
        'total'=>$total,
        'nborders'=>$nborders
        ] 
    );
        $dompdf->showPdfFile($html);
    }

    /**
     * @Route("/orders/pdf", name="_orders_pdf", methods="GET")
     */
    public function generateAllDepotsOrdersPdf(DepotRepository $depotRepository, SalesStatusRepository $salesStatusRepository, PdfLarge $pdfLarge)
    {
        $salesStatus = $salesStatusRepository->findOneBy(['name' => 'status']);
        $allDepots = $depotRepository->findAllOrdersByDepot($salesStatus->getStartAt(), $salesStatus->getEndAt());

        $pdfFileName = $salesStatus->getEndAt() === null ?
                'commandes_depuis_le_' . $salesStatus->getStartAt()->format('d-m-Y') :
                    'commandes_du_' . $salesStatus->getStartAt()->format('d-m-Y') . '_au_' . $salesStatus->getEndAt()->format('d-m-Y');

        $titlePdfView = ucfirst(str_replace('_', ' ', $pdfFileName));
        
        $pdfTwigView = $this->renderView('back/depot/pdf.html.twig', ['depots' => $allDepots, 'title' => $titlePdfView]);

        return $pdfLarge->showPdfFile($pdfTwigView, $pdfFileName);

    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/{id<\d+>}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Depot $depot, DepotRepository $depotRepository): Response
    {
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depotRepository->add($depot, true);

            return $this->redirectToRoute('app_back_depot_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/depot/edit.html.twig', [
            'depot' => $depot,
            'form' => $form,
        ]);
    }
    
    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/delete/{id<\d+>}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Depot $depot, DepotRepository $depotRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depot->getId(), $request->request->get('_token'))) {
            $depotRepository->remove($depot, true);
        }

        return $this->redirectToRoute('app_back_depot_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Modify the available status of a depot
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/available/{id<\d+>}", name="_available-status", methods={"POST"})
     */
    public function changeOnSaleStatus(Depot $depot, EntityManagerInterface $em)
    {
        if ($depot !== null) {
            $depot->setAvailable(!$depot->isAvailable());
            $em->flush();
            $data['depotId'] = $depot->getId();
            return $this->json($data, Response::HTTP_OK);
        }
    }
    
}