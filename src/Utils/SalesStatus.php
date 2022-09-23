<?php

namespace App\Utils;

use App\Repository\SalesStatusRepository;

/**
 * This class return the status of the sales in BDD
 */
class SalesStatus
{
    private $salesStatusRepository;

    public function __construct(SalesStatusRepository $salesStatusRepository)
    {
        $this->salesStatusRepository = $salesStatusRepository;
    }

    public function isSalesEnabled()
    {
        return $this->salesStatusRepository->findOneBy(['name' => 'status'])->isEnable();
    }
}