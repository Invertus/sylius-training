<?php

declare(strict_types=1);

namespace App\Order;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;

final class InventoryAwareOrderModifier implements OrderModifierInterface
{
    /**
     * @var OrderModifierInterface
     */
    private $decoratedOrderModifier;

    /**
     * @var OrderInventoryOperatorInterface
     */
    private $orderInventoryOperator;

    public function __construct(
        OrderModifierInterface $decoratedOrderModifier,
        OrderInventoryOperatorInterface $orderInventoryOperator
    ) {
        $this->decoratedOrderModifier = $decoratedOrderModifier;
        $this->orderInventoryOperator = $orderInventoryOperator;
    }

    public function addToOrder(OrderInterface $cart, OrderItemInterface $cartItem): void
    {
        $this->orderInventoryOperator->cancel($cart);

        $this->decoratedOrderModifier->addToOrder($cart, $cartItem);

        $this->orderInventoryOperator->hold($cart);
    }

    public function removeFromOrder(OrderInterface $cart, OrderItemInterface $item): void
    {
        $this->orderInventoryOperator->cancel($cart);

        $this->decoratedOrderModifier->removeFromOrder($cart, $item);

        $this->orderInventoryOperator->hold($cart);
    }
}
