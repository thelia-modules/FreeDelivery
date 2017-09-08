<?php

namespace FreeDelivery\EventListener;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;

class FreeDeliveryEventListener implements EventSubscriberInterface
{
    public function processPostage(DeliveryPostageEvent $event)
    {
        $taxCountry = $event->getCountry();

        if ("yes" === FreeDelivery::getConfigValue('freedelivery_use_tax')) {
            $cartTotalAmount = $event->getCart()->getTaxedAmount($event->getCountry());
        } else {
            $cartTotalAmount = $event->getCart()->getTotalAmount();
        }

        $moduleId = $event->getModule()->getModuleModel()->getId();

        foreach ($taxCountry->getAreas() as $area) {
            $freeDeliveryCondition = FreeDeliveryConditionQuery::create()
                ->filterByModuleId($moduleId)
                ->filterByAreaId($area->getId())
                ->findOne();

            if ($freeDeliveryCondition != null && $cartTotalAmount >= $freeDeliveryCondition->getAmount()) {
                $event->setPostage(0);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::MODULE_DELIVERY_GET_POSTAGE => ['processPostage', 64]
        ];
    }
}
