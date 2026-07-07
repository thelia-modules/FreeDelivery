<?php

declare(strict_types=1);

namespace FreeDelivery\Hook;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryCondition;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Model\AreaQuery;
use Thelia\Model\ModuleQuery;

class HookManager extends BaseHook
{
    public function __construct(
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'module.config-js' => [
                ['type' => 'back', 'method' => 'onModuleConfigJs'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $deliveryModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByCategory('delivery')
            ->find();

        $areas = AreaQuery::create()->find();

        $freeDeliveryConditionResult = [];
        /** @var FreeDeliveryCondition $freeDeliveryCondition */
        foreach (FreeDeliveryConditionQuery::create()->find() as $freeDeliveryCondition) {
            $freeDeliveryConditionResult[$freeDeliveryCondition->getModuleId()][$freeDeliveryCondition->getAreaId()]
                = $freeDeliveryCondition->getAmount();
        }

        $useTaxes = 'yes' === FreeDelivery::getConfigValue('freedelivery_use_tax');

        $event->add(
            $this->render(
                'FreeDelivery/module-configuration.html.twig',
                [
                    'deliveryModules' => $deliveryModules,
                    'areas' => $areas,
                    'freeDeliveryConditionResult' => $freeDeliveryConditionResult,
                    'useTaxes' => $useTaxes,
                ]
            )
        );
    }

    public function onModuleConfigJs(HookRenderEvent $event): void
    {
        $event->add(
            $this->render('FreeDelivery/module-config-js.html.twig')
        );
    }
}
