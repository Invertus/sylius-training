<?php

declare(strict_types=1);

namespace App\OrderProcessor;

use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class ExtraFeeOrderProcessor implements OrderProcessorInterface
{
    public const ADJUSTMENT_EXTRA_FEE = 'extra_fee';

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function process(OrderInterface $order): void
    {
        $order->removeAdjustments(self::ADJUSTMENT_EXTRA_FEE);

        if ($order->getItems()->count() < 3) {
            // create adjustment for $20, not neutral and add it on the order
            $adjustment = $this->adjustmentFactory->createWithData(
                self::ADJUSTMENT_EXTRA_FEE,
                'Extra fee for buying less than three products',
                2000 // 20 of the used currency
            );

            $order->addAdjustment($adjustment);
        }
    }
}
