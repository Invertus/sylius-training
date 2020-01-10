<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Entity\Supplier;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     * @psalm-var class-string
     */
    private $supplierClass;

    public function __construct(string $supplierClass)
    {
        $this->supplierClass = $supplierClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplier', EntityType::class, [
                'choice_label' => 'name',
                'class' => $this->supplierClass,
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): array
    {
        return [ProductVariantType::class];
    }
}
