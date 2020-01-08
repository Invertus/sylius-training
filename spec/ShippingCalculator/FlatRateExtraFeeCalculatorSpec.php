<?php

declare(strict_types=1);

namespace spec\App\ShippingCalculator;

use App\ShippingCalculator\FlatRateExtraFeeCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;

final class FlatRateExtraFeeCalculatorSpec extends ObjectBehavior
{
    function it_implements_shipping_calculator_interface(): void
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_flat_rate_type(CalculatorInterface $calculator): void
    {
        $calculator->getType()->willReturn('flat_rate_with_extra_fee');

        $this->getType()->shouldReturn('flat_rate_with_extra_fee');
    }

    function it_calculates_the_flat_rate_amount_configured_on_the_method(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $shipment->getOrder()->willReturn($order);
        $shipment->getShippables()->willReturn(new ArrayCollection());
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]])->shouldReturn(1500);
    }

    function it_adds_extra_fee_if_there_are_jeans_in_the_products(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $jeansTaxon,
        TaxonInterface $otherTaxon
    ): void
    {
        $shipment->getOrder()->willReturn($order);
        $shipment->getShippables()->willReturn(new ArrayCollection([
            $productVariant->getWrappedObject(),
        ]));

        $productVariant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([
            $otherTaxon->getWrappedObject(),
            $jeansTaxon->getWrappedObject(),
        ]));

        $otherTaxon->getCode()->willReturn('other');
        $jeansTaxon->getCode()->willReturn('jeans');

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]])->shouldReturn(3500);
    }

    function it_adds_only_one_extra_fee_even_if_there_are_more_jeans_produts(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ProductVariantInterface $firstJeansProductVariant,
        ProductInterface $firstJeansProduct,
        ProductVariantInterface $secondJeansProductVariant,
        ProductInterface $secondJeansProduct,
        TaxonInterface $jeansTaxon,
        TaxonInterface $otherTaxon
    ): void {
        $shipment->getOrder()->willReturn($order);
        $shipment->getShippables()->willReturn(new ArrayCollection([
            $firstJeansProductVariant->getWrappedObject(),
            $secondJeansProductVariant->getWrappedObject(),
        ]));

        $firstJeansProductVariant->getProduct()->willReturn($firstJeansProduct);
        $firstJeansProduct->getTaxons()->willReturn(new ArrayCollection([
            $otherTaxon->getWrappedObject(),
            $jeansTaxon->getWrappedObject(),
        ]));

        $secondJeansProductVariant->getProduct()->willReturn($secondJeansProduct);
        $secondJeansProduct->getTaxons()->willReturn(new ArrayCollection([
            $jeansTaxon->getWrappedObject(),
            $otherTaxon->getWrappedObject(),
        ]));

        $otherTaxon->getCode()->willReturn('other');
        $jeansTaxon->getCode()->willReturn('jeans');

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]])->shouldReturn(3500);
    }

    function it_does_not_add_extra_fee_if_there_are_no_jeans_in_the_products(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $otherTaxon
    ): void
    {
        $shipment->getOrder()->willReturn($order);
        $shipment->getShippables()->willReturn(new ArrayCollection([
            $productVariant->getWrappedObject(),
        ]));

        $productVariant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([
            $otherTaxon->getWrappedObject(),
        ]));

        $otherTaxon->getCode()->willReturn('other');

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]])->shouldReturn(1500);
    }

    function it_throws_a_channel_not_defined_exception_if_channel_code_key_does_not_exist(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ShippingMethodInterface $shippingMethod
    ): void {
        $shipment->getOrder()->willReturn($order);
        $shipment->getMethod()->willReturn($shippingMethod);

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $channel->getName()->willReturn('WEB');

        $shippingMethod->getName()->willReturn('UPS');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculate', [$shipment, ['amount' => 1500]])
        ;
    }
}
