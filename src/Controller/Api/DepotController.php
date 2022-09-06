<?php

namespace App\Controller\Api;

use App\Repository\DepotRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/** @Route("/api/v1/depots", name="api_v1_depots" )
 * 
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
        $allDepots = $depotRepository->findAll();
        $availableDepots = $depotRepository->findBy(['available' => true]);

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_depots_list'],
            ['data' => $availableDepots]
        );
    }

    /**
     * Show one depot with given slug
     * @Route("/{slug}", name="_show", methods="GET")
     */
    public function show(string $slug, DepotRepository $depotRepository): Response
    {
        $depot = $depotRepository->findOneBy(['slug' => $slug]);

        if ($depot === null)
        {
            return $this->prepareResponse(
                'Pas de dépôt trouvé avec ce slug',
                [],
                [],
                true,
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->prepareResponse(
            'OK',
            ['groups' => 'api_v1_depot_show'],
            ['data' => $depot]
        );

    }

    private function prepareResponse(
        string $message, 
        array $options = [], 
        array $data = [], 
        bool $isError = false, 
        int $httpCode = 200, 
        array $headers = []
    )
    {
        $responseData = [
            'error' => $isError,
            'message' => $message,
        ];

        foreach ($data as $key => $value)
        {
            $responseData[$key] = $value;
        }
        return $this->json($responseData, $httpCode, $headers, $options);
    }
    
}