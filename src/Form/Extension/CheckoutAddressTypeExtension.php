<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CheckoutAddressTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('differentShippingAddress', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Different shipping address?',
            ])
        ;

        // And replaces it with the one that fallbacks shipping data to billing data
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $orderData = $event->getData();

                if (isset($orderData['billingAddress']) && (!isset($orderData['differentShippingAddress']) || false === $orderData['differentShippingAddress'])) {
                    $orderData['shippingAddress'] = $orderData['billingAddress'];
                    unset($orderData['shippingAddress']['vatNumber']);
                }

                $orderData['differentBillingAddress'] = true;

                $event->setData($orderData);
            }, 100)
        ;
    }

    public static function getExtendedTypes(): array
    {
        return [AddressType::class];
    }
}
