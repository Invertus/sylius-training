<?php

declare(strict_types=1);

namespace App\Listener;

use App\TrackingNumberProviderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class AssignTrackingCodeToShipmentListener
{
    /**
     * @var TrackingNumberProviderInterface
     */
    private $trackingNumberProvider;

    public function __construct(
        TrackingNumberProviderInterface $trackingNumberProvider
    ) {
        $this->trackingNumberProvider = $trackingNumberProvider;
    }

    public function __invoke(ShipmentInterface $shipment): void
    {
        $shipment->setTracking($this->trackingNumberProvider->provide($shipment));
    }
}
