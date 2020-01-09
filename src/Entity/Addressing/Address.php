<?php

declare(strict_types=1);

namespace App\Entity\Addressing;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_address")
 */
class Address extends BaseAddress
{
    /**
     * @ORM\Column(name="vat_number", nullable=true)
     * @var string|null
     */
    private $vatNumber;

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
    }
}
