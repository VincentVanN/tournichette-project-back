<?php

namespace App\Controller\Back;

use App\Form\SalesMailType;
use App\Repository\SalesStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back/mailing", name="app_back_mailing")
 */
class MailingController extends AbstractController
{
    
    /**
     * @Route("/sales", name="_update_sales", methods={"GET", "POST"})
     */
    public function update(Request $request, SalesStatusRepository $salesStatusRepository): Response
    {
        $saleStatus = $salesStatusRepository->findOneBy(['name' => 'status']);
        $form = $this->createForm(SalesMailType::class, $saleStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salesStatusRepository->add($saleStatus, true);

            $this->addFlash('success', 'Les mails ont été enregistrés avec succes.');
        }

        return $this->renderForm('back/mailing/sales/edit.html.twig', [
            'salesStatus' => $saleStatus,
            'form' => $form
        ]);
    }
}
