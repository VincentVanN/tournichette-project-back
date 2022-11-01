<?php

namespace App\Controller\Back;

use App\Form\SalesMailType;
use App\Repository\SalesStatusRepository;
use App\Repository\UserRepository;
use App\Utils\CustomMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

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

    /**
     * @Route("/sales/show", name="_show_sales", methods={"GET"})
     */
    public function show(SalesStatusRepository $salesStatusRepository, UserRepository $userRepository, CustomMailer $mailer): Response
    {
        $saleStatus = $salesStatusRepository->findOneBy(['name' => 'status']);

        $subscribedUsers = $userRepository->findBy(['emailChecked' => true, 'emailNotifications' => true]);

        // dd($subscribedUsers);
        $subject = $saleStatus->isEnable() ? $saleStatus->getStartMailSubject() : $saleStatus->getEndMailSubject();
        $message = $saleStatus->isEnable() ? $saleStatus->getStartMail() : $saleStatus->getEndMail();

        // dd($subject, $message);

        $mailer->sendSalesNotification($subscribedUsers, $subject, $message);

        return $this->render('mailer/email_status_sales/show.html.twig', [
            'message' => $message
        ]);
    }
}
