<?php

namespace FreeDelivery\Hook;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryCondition;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\AreaQuery;
use Thelia\Model\ModuleQuery;

/**
 * Class HookManager
 * @package FreeDelivery\Hook
 */
class HookManager extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $freeDeliveryConditionResult = [];

        $deliveryModules = ModuleQuery::create()
            ->findByCategory('delivery');

        $areas = AreaQuery::create()->find();

        $freeDeliveryConditionCollection = FreeDeliveryConditionQuery::create()->find();

        $useTaxes = (FreeDelivery::getConfigValue('freedelivery_use_tax') == "yes");

        if (null !== $freeDeliveryConditionCollection) {
            /** @var FreeDeliveryCondition $freeDeliveryCondition */
            foreach ($freeDeliveryConditionCollection as $freeDeliveryCondition) {
                $freeDeliveryConditionResult[$freeDeliveryCondition->getModuleId()][$freeDeliveryCondition->getAreaId()] = $freeDeliveryCondition->getAmount();
            }
        }

        $event->add(
            $this->render(
                'module_configuration.html',
                compact('deliveryModules', 'areas', 'freeDeliveryConditionResult', 'useTaxes')
            )
        );
    }
}
