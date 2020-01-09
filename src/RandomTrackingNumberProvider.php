<?php

declare(strict_types=1);

namespace App;

use Sylius\Component\Core\Model\ShipmentInterface;

final class RandomTrackingNumberProvider implements TrackingNumberProviderInterface
{
    public function provide(ShipmentInterface $shipment): string
    {
        return substr(md5(uniqid('', true)), 0, 10);
    }
}
