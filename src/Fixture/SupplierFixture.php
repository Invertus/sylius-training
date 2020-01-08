<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Supplier;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SupplierFixture extends AbstractFixture
{
    /** @var ObjectManager */
    private $supplierManager;

    /** @var FactoryInterface */
    private $supplierFactory;

    /** @var \Faker\Generator */
    private $faker;

    public function __construct(
        ObjectManager $supplierManager,
        FactoryInterface $supplierFactory
    ) {
        $this->supplierManager = $supplierManager;
        $this->supplierFactory = $supplierFactory;
        $this->faker = \Faker\Factory::create();
    }

    public function load(array $options): void
    {
        for ($i = 0; $i < $options['count']; ++$i) {
            /** @var Supplier $supplier */
            $supplier = $this->supplierFactory->createNew();
            $supplier->setEmail($this->faker->email);
            $supplier->setName($this->faker->name);

            $this->supplierManager->persist($supplier);
        }

        $this->supplierManager->flush();
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode->children()->integerNode('count');
    }

    public function getName(): string
    {
        return 'supplier';
    }
}
