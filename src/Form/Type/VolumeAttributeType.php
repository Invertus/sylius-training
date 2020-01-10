<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VolumeAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', IntegerType::class)
            ->add('unit', ChoiceType::class, [
                'choices' => [
                    'mililiters' => 'ml',
                    'liters' => 'l',
                ],
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => false,
            ])
            ->setRequired('configuration')
            ->setDefined('locale_code')
        ;
    }
}
