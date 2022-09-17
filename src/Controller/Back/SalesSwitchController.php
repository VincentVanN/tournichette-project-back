<?php

namespace App\Controller\Back;

use App\Utils\SalesStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SalesSwitchController extends AbstractController
{
    /**
     * Check sales status to set position switch in templates
     */
    public function setSalesSwitchPosition(SalesStatus $salesStatus): Response
    {
        // $salesStatus = new SalesStatus();

        // dump($salesStatus->isSalesEnabled());
        return $this->render('_switch-sales.html.twig', [
            'salesStatus' => $salesStatus->isSalesEnabled(),
        ]);
    }
}
