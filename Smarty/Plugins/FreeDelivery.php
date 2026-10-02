<?php

declare(strict_types=1);

namespace FreeDelivery\Smarty\Plugins;

use FreeDelivery\Service\FreeDeliveryResolver;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

/**
 * Legacy Smarty plugin kept for backward compatibility.
 *
 * The resolution logic lives in {@see FreeDeliveryResolver}, shared with the
 * Twig extension {@see \FreeDelivery\Twig\FreeDeliveryExtension}.
 */
class FreeDelivery extends AbstractSmartyPlugin
{
    public function __construct(
        private readonly FreeDeliveryResolver $resolver,
    ) {
    }

    public function getPluginDescriptors(): array
    {
        return [
            new SmartyPluginDescriptor('function', 'free_delivery', $this, 'getFreeDelivery'),
        ];
    }

    /**
     * @param array $params
     * @param \Smarty_Internal_Template $smarty
     */
    public function getFreeDelivery($params, $smarty): void
    {
        $countryId = isset($params['country_id']) ? (int) $params['country_id'] : null;

        $smarty->assign('free_deliveries', $this->resolver->resolveForCountry($countryId));
    }
}
