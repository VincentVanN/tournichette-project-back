<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_back_homepage")
     */
    public function index(): Response
    {
        return $this->render('back/homepage/homepage.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
