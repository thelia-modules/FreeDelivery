<?php

declare(strict_types=1);

namespace FreeDelivery\Service;

use FreeDelivery\Model\FreeDeliveryCondition;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\AddressQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;

/**
 * Shared resolution of the free-delivery conditions applicable to a country.
 *
 * Consumed by both the (legacy) Smarty plugin and the Twig extension so the
 * front-office logic lives in a single place.
 */
final readonly class FreeDeliveryResolver
{
    public function __construct(
        private RequestStack $requestStack,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    /**
     * @return array{best_free_delivery: FreeDeliveryCondition, list: FreeDeliveryCondition[]}
     */
    public function resolveForCountry(?int $countryId = null): array
    {
        $bestFreeDelivery = (new FreeDeliveryCondition())->setAmount('0');
        $result = [
            'best_free_delivery' => $bestFreeDelivery,
            'list' => [],
        ];

        $countryId ??= $this->resolveCurrentCountryId();

        $country = CountryQuery::create()->findOneById($countryId)
            ?? CountryQuery::create()->findOneByByDefault(1);

        if (null === $country) {
            return $result;
        }

        $areaIds = array_map(
            static fn ($area): int => $area->getId(),
            iterator_to_array($country->getCountryAreas())
        );

        if ([] === $areaIds) {
            return $result;
        }

        $activatedDeliveryModuleIds = array_map('intval', ModuleQuery::create()
            ->filterByType(BaseModule::DELIVERY_MODULE_TYPE)
            ->filterByActivate(1)
            ->select('id')
            ->find()
            ->toArray());

        $freeDeliveryConditions = FreeDeliveryConditionQuery::create()
            ->filterByAreaId($areaIds, Criteria::IN)
            ->find();

        /** @var FreeDeliveryCondition $freeDeliveryCondition */
        foreach ($freeDeliveryConditions as $freeDeliveryCondition) {
            if (!\in_array((int) $freeDeliveryCondition->getModuleId(), $activatedDeliveryModuleIds, true)) {
                continue;
            }

            $result['list'][] = $freeDeliveryCondition;

            $currentAmount = (float) $freeDeliveryCondition->getAmount();
            $bestAmount = (float) $result['best_free_delivery']->getAmount();

            if ($currentAmount < $bestAmount || 0.0 === $bestAmount) {
                $result['best_free_delivery'] = $freeDeliveryCondition;
            }
        }

        return $result;
    }

    private function resolveCurrentCountryId(): ?int
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        $session = $request->getSession();
        if (!$session instanceof Session) {
            return null;
        }

        $addressId = $session->getSessionCart($this->dispatcher)->getAddressDeliveryId();
        if (null === $addressId) {
            return null;
        }

        return AddressQuery::create()->findPk($addressId)?->getCountryId();
    }
}
