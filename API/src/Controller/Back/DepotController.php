<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Repository\DepotRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/back/depot")
 */
class DepotController extends AbstractController
{
    /**
     * @Route("/depot", name="app_depot")
     */
    public function index(): Response
    {
        return $this->render('depot/index.html.twig', [
            'controller_name' => 'DepotController',
        ]);
    }
}
