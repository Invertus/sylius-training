<?php

declare(strict_types=1);

namespace App\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;

final class ReleaseOrderInventoryWhenOrderIsRemovedListener
{
    /**
     * @var OrderInventoryOperatorInterface
     */
    private $orderInventoryOperator;

    /**
     * @var ObjectManager
     */
    private $productVariantManager;

    public function __construct(
        OrderInventoryOperatorInterface $orderInventoryOperator,
        ObjectManager $productVariantManager
    ) {
        $this->orderInventoryOperator = $orderInventoryOperator;
        $this->productVariantManager = $productVariantManager;
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $this->orderInventoryOperator->cancel($event->getSubject());

        $this->productVariantManager->flush();
    }
}
