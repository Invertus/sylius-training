<?php

declare(strict_types=1);

namespace spec\App\ShippingCalculator;

use App\ShippingCalculator\FlatRateExtraFeeCalculator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
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
