<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class SupplierMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $event
            ->getMenu()
                ->getChild('catalog')
                    ->addChild('suppliers', ['route' => 'app_admin_supplier_index'])
                        ->setLabel('app.ui.suppliers')
                        ->setLabelAttribute('icon', 'dolly')
        ;
    }
}
