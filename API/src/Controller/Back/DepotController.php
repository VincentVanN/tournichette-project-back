<?php

namespace App\Controller\Back;

use App\Entity\Depot;
use App\Form\DepotType;
use App\Repository\DepotRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\DepotPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
       // $allDepots = $depotRepository->findAll();

        return $this->render('back/depot/index.html.twig',
            ['depots' => $depotRepository->findAll(),
        ]);
    }

    
    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DepotRepository $depotRepository): Response
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

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
    public function show(Depot $depot): Response
    {
        return $this->render('back/depot/show.html.twig', [
            'depot' => $depot,
        ]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Depot $depot, DepotRepository $depotRepository): Response
    {
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on slugify le titre fournit par le user avant de l'enregistrer en BDD
            // $depot->setSlug($mySlugger->slugify($depot->getTitle()));
            $depotRepository->add($depot, true);

            return $this->redirectToRoute('app_back_depot_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/depot/edit.html.twig', [
            'depot' => $depot,
            'form' => $form,
        ]);
    }
    
    /**
     * @Route("/delete/{id<\d+>}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Depot $depot, DepotRepository $depotRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$depot->getId(), $request->request->get('_token'))) {
            $depotRepository->remove($depot, true);
        }

        return $this->redirectToRoute('app_back_depot_list', [], Response::HTTP_SEE_OTHER);
    }
    
}
