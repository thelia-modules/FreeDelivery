<?php

namespace FreeDelivery\Smarty\Plugins;

use FreeDelivery\Model\FreeDeliveryCondition;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\CountryQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\TaxEngine\TaxEngine;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class FreeDelivery extends AbstractSmartyPlugin
{
    /** @var ContainerInterface Service Container */
    protected $container = null;

    /** @var \Thelia\Core\HttpFoundation\Request The Request */
    protected $currentRequest;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var  TaxEngine */
    protected $taxEngine;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->currentRequest = $container->get('request_stack')->getCurrentRequest();

        $this->dispatcher = $container->get('event_dispatcher');

        $this->taxEngine = $container->get('thelia.taxEngine');
    }

    public function getPluginDescriptors()
    {
        return [
            new SmartyPluginDescriptor('function', 'free_delivery', $this, 'getFreeDelivery')
        ];
    }

    /**
     * @param array $params
     * @param \Smarty_Internal_Template $smarty
     */
    public function getFreeDelivery($params, $smarty)
    {
        $freeDeliveries = [];
        $freeDeliveries['best_free_delivery'] = null;

        $countryId = $params['country_id'];

        if (null === $countryId) {
            $session = $this->currentRequest->getSession();
            $cart = $session->getSessionCart($this->dispatcher);
            $deliveryAddress = $cart->getAddressRelatedByAddressDeliveryId();
            if (null !== $deliveryAddress) {
                $countryId = $deliveryAddress->getCountryId();
            }
        }

        $country = CountryQuery::create()
            ->findOneById($countryId);

        if (null === $country) {
            $country = CountryQuery::create()
                ->findOneByByDefault(1);
        }

        $areas = $country->getAreas();
        $areasString = "";

        foreach ($areas as $area) {
            $areasString .= $area->getId().',';
        }

        $freeDeliveryConditions = FreeDeliveryConditionQuery::create()
            ->filterByAreaId($areasString, Criteria::IN);

        $activatedDeliveryModules = ModuleQuery::create()
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE)
            ->filterByActivate(1)
            ->select(['id'])
            ->find()
            ->toArray();

        /** @var FreeDeliveryCondition $freeDeliveryCondition */
        foreach ($freeDeliveryConditions as $freeDeliveryCondition) {
            if (in_array($freeDeliveryCondition->getModuleId(), $activatedDeliveryModules)) {
                $freeDeliveries['list'][] = $freeDeliveryCondition;
                if ($freeDeliveryCondition->getAmount() < $freeDeliveries['best_free_delivery']
                    || null === $freeDeliveries['best_free_delivery']
                ) {
                    $freeDeliveries['best_free_delivery'] = $freeDeliveryCondition;
                }
            }
        }

        $smarty->assign('free_deliveries', $freeDeliveries);
    }
}
