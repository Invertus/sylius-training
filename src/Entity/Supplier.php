<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_supplier")
 */
class Supplier implements ResourceInterface
{
    public const STATE_UNVERIFIED = 'unverified';
    public const STATE_VERIFIED = 'verified';

    public const TRANSITION_VERIFY = 'verify';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $id;

    /**
     * @ORM\Column(nullable=false)
     * @Assert\NotBlank()
     *
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(nullable=false)
     *
     * @var string
     */
    private $state = self::STATE_UNVERIFIED;

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
