<?php

declare(strict_types=1);

namespace App\OrderProcessor;

use App\Entity\Addressing\Address;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class TaxesOrderProcessor implements OrderProcessorInterface
{
    /**
     * @var OrderProcessorInterface
     */
    private $decoratedOrderProcessor;

    public function __construct(OrderProcessorInterface $decoratedOrderProcessor)
    {
        $this->decoratedOrderProcessor = $decoratedOrderProcessor;
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var Address $billingAddress */
        $billingAddress = $order->getBillingAddress();
        if ($billingAddress !== null && $billingAddress->getVatNumber() !== null) {
            return;
        }

        $this->decoratedOrderProcessor->process($order);
    }
}
