<?php

declare(strict_types=1);

namespace App;

use App\Entity\Supplier;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class SyliusMailerVerifiedSupplierNotifier implements VerifiedSupplierNotifierInterface
{
    /** @var SenderInterface */
    private $emailer;

    public function __construct(SenderInterface $emailer)
    {
        $this->emailer = $emailer;
    }

    public function notify(Supplier $supplier): void
    {
        $this->emailer->send(
            'supplier_verified',
            [$supplier->getEmail()],
            ['name' => $supplier->getName()]
        );
    }
}
