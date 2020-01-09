<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OneClickCheckoutController
{
    /** @var FactoryInterface */
    private $orderFactory;

    /** @var ShopperContextInterface */
    private $shopperContext;

    /** @var FactoryInterface */
    private $orderItemFactory;

    /**
     * @var RepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        FactoryInterface $orderFactory,
        ShopperContextInterface $shopperContext,
        FactoryInterface $orderItemFactory,
        RepositoryInterface $productVariantRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        StateMachineFactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->orderFactory = $orderFactory;
        $this->shopperContext = $shopperContext;
        $this->orderItemFactory = $orderItemFactory;
        $this->productVariantRepository = $productVariantRepository;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function buyAction(
        int $id
    ): Response {
        // create an order
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        // set up the order data (channel, currency, locale, customer)
        /** @var ChannelInterface $channel */
        $channel = $this->shopperContext->getChannel();

        /** @var CustomerInterface $customer */
        $customer = $this->shopperContext->getCustomer();

        $order->setChannel($channel);
        $order->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $order->setLocaleCode($this->shopperContext->getLocaleCode());
        $order->setCustomer($customer);

        // add an order item corresponding to the given product variant id
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->orderItemFactory->createNew();
        $orderItem->setVariant($this->productVariantRepository->find($id));
        $order->addItem($orderItem);
        $this->orderItemQuantityModifier->modify($orderItem, 1);

        // fill in the shipping and billing addresses using the default address
        $order->setShippingAddress(clone $customer->getDefaultAddress());
        $order->setBillingAddress(clone $customer->getDefaultAddress());

        // go through checkout state machine
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        // persist and flush the created order
        $this->orderManager->persist($order);
        $this->orderManager->flush();

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_show', ['number' => $order->getNumber()]));
    }
}
