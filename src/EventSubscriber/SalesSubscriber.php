<?php

namespace App\EventSubscriber;

use App\Utils\SalesStatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SalesSubscriber implements EventSubscriberInterface
{
    /**
     * List of routes that will trigger the event
     */
    private $salesRoutesArray = [
        'api_v1_carts_list',
        'api_v1_carts_show',
        'api_v1_depots_list',
        'api_v1_depots_show',
        'api_v1_orders_create',
        'api_v1_orders_show',
        'api_v1_orders_user',
        'api_v1_products_list',
        'api_v1_products_show',
    ];

    private $salesStatus;

    public function __construct(SalesStatus $salesStatus)
    {
        $this->salesStatus = $salesStatus;
    }

    /**
     * If the route is part of the event triggers, return a json Response if sales are disabled
     */
    public function onSalesRequest(RequestEvent $event)
    {
        
        if(in_array($event->getRequest()->get('_route'), $this->salesRoutesArray) && $this->salesStatus->isSalesEnabled() !== true) {
            return $event->setResponse(new JsonResponse(['error' => true, 'message' => 'Sales disabled'], Response::HTTP_LOCKED));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onSalesRequest',
        ];
    }
}
