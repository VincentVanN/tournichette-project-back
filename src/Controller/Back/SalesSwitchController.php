<?php

namespace App\Controller\Back;

use App\Repository\SalesStatusRepository;
use App\Utils\SalesStatus;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SalesSwitchController extends AbstractController
{
    /**
     * Check sales status to set position switch in templates
     */
    public function setSalesSwitchPosition(SalesStatus $salesStatus): Response
    {
        return $this->render('_switch-sales.html.twig', [
            'salesStatus' => $salesStatus->isSalesEnabled(),
        ]);
    }

    /**
     * Enabling / desabling sales status
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/back/sales/{status<(enable|disable)>}", name="app_back_sales_enable", methods="GET")
     */
    public function enablingSales(
        EntityManagerInterface $em,
        SalesStatusRepository $salesStatusRepository,
        SalesStatus $salesStatusService,
        $status
    ): Response
    {
        $salesStatus = $salesStatusRepository->findOneBy(['name' => 'status']);
        $status = $status == 'enable' ? true : false;
        
        if($salesStatus !== null) {
            $salesStatus->setEnable($status);
            $em->flush();

            // Verifying if success
            if($salesStatusService->isSalesEnabled() === $status) {
                return $this->json('sales status changed', Response::HTTP_OK);
            }
        }

        return $this->json('sales status changed failed', Response::HTTP_NOT_MODIFIED);

    }
}
