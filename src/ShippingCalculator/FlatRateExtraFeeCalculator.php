<?php

declare(strict_types=1);

namespace App\ShippingCalculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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

        $price = (int) $configuration[$channelCode]['amount'];

        if ($this->shouldExtraFeeBeAdded($subject)) {
            $price += (int) $configuration[$channelCode]['fee'];
        }

        return $price;
    }

    public function getType(): string
    {
        return 'flat_rate_with_extra_fee';
    }

    private function shouldExtraFeeBeAdded(BaseShipmentInterface $subject): bool
    {
        // add $20 if there are any products from Jeans category
        // there's only one fee even if there's multiple jeans in the order

        /** @var ProductVariantInterface[] $productVariants */
        $productVariants = $subject->getShippables();

        foreach ($productVariants as $productVariant) {
            /** @var ProductInterface $product */
            $product = $productVariant->getProduct();

            $taxons = $product->getTaxons()->toArray();
            $taxonCodes = array_map(
                function (TaxonInterface $taxon): string {
                    return $taxon->getCode();
                },
                $taxons
            );

            if (in_array('jeans', $taxonCodes, true)) {
                return true;
            }
        }

        return false;
    }
}
