<?php

declare(strict_types=1);

namespace FreeDelivery\Twig;

use FreeDelivery\Service\FreeDeliveryResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FreeDeliveryExtension extends AbstractExtension
{
    public function __construct(
        private readonly FreeDeliveryResolver $resolver,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('free_delivery', $this->getFreeDelivery(...)),
        ];
    }

    /**
     * @return array{best_free_delivery: \FreeDelivery\Model\FreeDeliveryCondition, list: \FreeDelivery\Model\FreeDeliveryCondition[]}
     */
    public function getFreeDelivery(?int $countryId = null): array
    {
        return $this->resolver->resolveForCountry($countryId);
    }
}
