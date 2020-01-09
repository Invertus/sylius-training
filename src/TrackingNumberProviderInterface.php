<?php

declare(strict_types=1);

namespace App;

use Sylius\Component\Core\Model\ShipmentInterface;

interface TrackingNumberProviderInterface
{
    public function provide(ShipmentInterface $shipment): string;
}
