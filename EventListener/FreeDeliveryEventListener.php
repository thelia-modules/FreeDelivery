<?php

namespace FreeDelivery\EventListener;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use FreeDelivery\Model\Map\FreeDeliveryConditionTableMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;

class FreeDeliveryEventListener implements EventSubscriberInterface
{
    public function processPostage(DeliveryPostageEvent $event)
    {
        $deliveryCountry = $event->getCountry();
        $deliveryState = null;

        if ($deliveryCountry === null) {
            return;
        }

        if (null !== $deliveryAddress = $event->getAddress()) {
            $deliveryState = $deliveryAddress->getState();
        }

        if ("yes" === FreeDelivery::getConfigValue('freedelivery_use_tax')) {
            $cartTotalAmount = $event->getCart()->getTaxedAmount($deliveryCountry, true, $deliveryState);
        } else {
            $cartTotalAmount = $event->getCart()->getTotalAmount();
        }

        $moduleId = $event->getModule()->getModuleModel()->getId();


        if ($deliveryState !== null) {
            $freeDeliveryConditionCollection = FreeDeliveryConditionQuery::create()
                ->filterByModuleId($moduleId)
                ->useAreaQuery()
                    ->useCountryAreaQuery()
                        ->filterByStateId($deliveryState->getId())
                    ->endUse()
                ->endUse()
                ->find();


            if (!$freeDeliveryConditionCollection->isEmpty()) {
                foreach ($freeDeliveryConditionCollection as $freeDeliveryCondition) {
                    if ($cartTotalAmount >= $freeDeliveryCondition->getAmount()) {
                        $event->setPostage(0);
                        return;
                    }
                }
                return;
            }
        }

        $query = FreeDeliveryConditionQuery::create()
            ->filterByModuleId($moduleId)
            ->useAreaQuery()
                ->useCountryAreaQuery()
                    ->filterByCountryId($deliveryCountry->getId())
                    ->filterByStateId(null)
                ->endUse()
            ->endUse()
            ->where(FreeDeliveryConditionTableMap::TABLE_NAME.'.amount <= ?', $cartTotalAmount)
            ->find();

        if (!$query->isEmpty()) {
            $event->setPostage(0);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::MODULE_DELIVERY_GET_POSTAGE => ['processPostage', 64]
        ];
    }
}
