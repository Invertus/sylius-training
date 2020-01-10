<?php

declare(strict_types=1);

namespace App\Attribute;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class VolumeAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'volume';

    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_JSON;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function validate(
        AttributeValueInterface $attributeValue,
        ExecutionContextInterface $context,
        array $configuration
    ): void {
    }
}
