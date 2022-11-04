<?php

namespace App\Controller\Api;

use App\Utils\SalesStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/sales", name="api_v1_sales")
 */
class SalesController extends AbstractController
{
    /**
     * @Route("", name="_status", methods="GET")
     */
    public function showSalesStatus(SalesStatus $salesStatus): Response
    {

        if($salesStatus->isSalesEnabled() === true) {

            return $this->prepareResponse('Sales enabled', [], [], false, Response::HTTP_OK);
        } else {
            return $this->prepareResponse('Sales disabled', [], [], true, Response::HTTP_LOCKED);
        }
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
