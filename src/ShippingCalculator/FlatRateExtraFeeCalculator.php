<?php

declare(strict_types=1);

namespace App\ShippingCalculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Webmozart\Assert\Assert;

final class FlatRateExtraFeeCalculator implements CalculatorInterface
{
    /**
     * @throws MissingChannelConfigurationException
     */
    public function calculate(BaseShipmentInterface $subject, array $configuration): int
    {
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        $channelCode = $subject->getOrder()->getChannel()->getCode();

        if (!isset($configuration[$channelCode])) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no amount defined for shipping method %s',
                $subject->getOrder()->getChannel()->getName(),
                $subject->getMethod()->getName()
            ));
        }

        return ((int) $configuration[$channelCode]['amount']) + 2000;
    }

    public function getType(): string
    {
        return 'flat_rate_with_extra_fee';
    }
}
