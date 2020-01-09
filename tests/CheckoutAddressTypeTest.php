<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Addressing\Address;
use App\Entity\Order\Order;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class CheckoutAddressTypeTest extends KernelTestCase
{
    /**
     * @var FormInterface
     */
    private $form;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var FormFactoryInterface $factory */
        $factory = self::$container->get('form.factory');

        $this->form = $factory->create(AddressType::class, new Order());
    }

    /** @test */
    public function it_uses_billing_address_as_shipping_address_if_different_shipping_address_is_not_checked(): void
    {
        $this->form->submit([
            'billingAddress' => [
                'firstName' => 'First',
                'lastName' => 'Last',
                'countryCode' => 'US',
                'street' => 'Street',
                'city' => 'City',
                'postcode' => '01234',
                'vatNumber' => 'VAT',
            ],
            'differentShippingAddress' => false,
        ]);

        /** @var Order $data */
        $data = $this->form->getData();

        $this->compareAddressesExcludingVatNumber($data->getShippingAddress(), $data->getBillingAddress());
        Assert::assertSame('VAT', $data->getBillingAddress()->getVatNumber());
        Assert::assertNull($data->getShippingAddress()->getVatNumber());
    }

    /** @test */
    public function it_keeps_separate_billing_and_shipping_addresses_if_different_shipping_address_is_checked(): void
    {
        $this->form->submit([
            'billingAddress' => [
                'firstName' => 'First',
                'lastName' => 'Last',
                'countryCode' => 'US',
                'street' => 'Street',
                'city' => 'City',
                'postcode' => '01234',
                'vatNumber' => 'VAT',
            ],
            'shippingAddress' => [
                'firstName' => 'First120',
                'lastName' => 'Last120',
                'countryCode' => 'PL',
                'street' => 'Street120',
                'city' => 'City120',
                'postcode' => '98765',
            ],
            'differentShippingAddress' => true,
        ]);

        /** @var Order $data */
        $data = $this->form->getData();

        try {
            $this->compareAddressesExcludingVatNumber($data->getShippingAddress(), $data->getBillingAddress());

            $this->fail('Addresses should not be the same');
        } catch (ExpectationFailedException $exception) {
        }
    }

    /** @test */
    public function it_uses_billing_address_as_shipping_address_if_different_shipping_address_is_not_checked_even_if_shipping_address_is_provided(): void
    {
        $this->form->submit([
            'billingAddress' => [
                'firstName' => 'First',
                'lastName' => 'Last',
                'countryCode' => 'US',
                'street' => 'Street',
                'city' => 'City',
                'postcode' => '01234',
                'vatNumber' => 'VAT',
            ],
            'shippingAddress' => [
                'firstName' => 'First120',
                'lastName' => 'Last120',
                'countryCode' => 'PL',
                'street' => 'Street120',
                'city' => 'City120',
                'postcode' => '98765',
            ],
            'differentShippingAddress' => false,
        ]);

        /** @var Order $data */
        $data = $this->form->getData();

        $this->compareAddressesExcludingVatNumber($data->getShippingAddress(), $data->getBillingAddress());
        Assert::assertSame('VAT', $data->getBillingAddress()->getVatNumber());
        Assert::assertNull($data->getShippingAddress()->getVatNumber());
    }

    private function compareAddressesExcludingVatNumber(Address $firstAddress, Address $secondAddress): void
    {
        Assert::assertSame($firstAddress->getCity(), $secondAddress->getCity());
        Assert::assertSame($firstAddress->getCompany(), $secondAddress->getCompany());
        Assert::assertSame($firstAddress->getCountryCode(), $secondAddress->getCountryCode());
        Assert::assertSame($firstAddress->getCustomer(), $secondAddress->getCustomer());
        Assert::assertSame($firstAddress->getFirstName(), $secondAddress->getFirstName());
        Assert::assertSame($firstAddress->getLastName(), $secondAddress->getLastName());
        Assert::assertSame($firstAddress->getPhoneNumber(), $secondAddress->getPhoneNumber());
        Assert::assertSame($firstAddress->getPostcode(), $secondAddress->getPostcode());
        Assert::assertSame($firstAddress->getProvinceCode(), $secondAddress->getProvinceCode());
        Assert::assertSame($firstAddress->getProvinceName(), $secondAddress->getProvinceName());
        Assert::assertSame($firstAddress->getStreet(), $secondAddress->getStreet());
    }
}
