<?php

declare(strict_types=1);

namespace App\Promotion;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class RoundingDownUnitsPriceDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function execute(PromotionSubjectInterface $order, array $configuration, PromotionInterface $promotion): bool
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        // for every unit, create an adjustment to remove its decimal part (round down to hundreds)
        $adjustmentAdded = false;
        foreach ($order->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                $adjustment = $this->adjustmentFactory->createWithData(
                    AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
                    'Rounding down the price',
                    -1 * ($item->getUnitPrice() % 100)
                );
                $adjustment->setOriginCode(self::class);

                $unit->addAdjustment($adjustment);

                $adjustmentAdded = true;
            }
        }

        return $adjustmentAdded;
    }

    public function revert(PromotionSubjectInterface $order, array $configuration, PromotionInterface $promotion): void
    {
    }
}
