<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['shippable']) {
            $builder->add('vatNumber');
        }
    }

    public static function getExtendedTypes(): array
    {
        return [AddressType::class];
    }
}
