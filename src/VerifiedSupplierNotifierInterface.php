<?php

declare(strict_types=1);

namespace App;

use App\Entity\Supplier;

interface VerifiedSupplierNotifierInterface
{
    public function notify(Supplier $supplier): void;
}
