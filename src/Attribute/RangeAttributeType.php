<?php

declare(strict_types=1);

namespace App\Attribute;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RangeAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'range';

    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_INTEGER;
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
        $value = $attributeValue->getValue();

        $validationErrors = $context->getValidator()->validate($value, [
            new Range(['min' => $configuration['min'], 'max' => $configuration['max']])
        ]);

        foreach ($validationErrors as $error) {
            $context
                ->buildViolation($error->getMessage())
                ->atPath('value')
                ->addViolation()
            ;
        }
    }
}
