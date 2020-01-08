<?php

declare(strict_types=1);

namespace App;

use App\Entity\Supplier;
use Psr\Log\LoggerInterface;

final class LoggingVerifiedSupplierNotifier implements VerifiedSupplierNotifierInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function notify(Supplier $supplier): void
    {
        $this->logger->info(
            sprintf('Supplier "%s" has been verified!', $supplier->getName())
        );
    }
}
