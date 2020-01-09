<?php

declare(strict_types=1);

namespace spec\App\Promotion;

use App\Clock\ClockInterface;
use App\Promotion\BirthdayRuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class BirthdayRuleCheckerSpec extends ObjectBehavior
{
    function let(ClockInterface $clock)
    {
        $this->beConstructedWith($clock);
    }

    function it_is_a_rule_checker(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_is_not_eligible_if_passed_subject_is_not_an_order(PromotionSubjectInterface $subject): void
    {
        $this->isEligible($subject, [])->shouldReturn(false);
    }

    function it_is_not_eligible_if_there_is_no_customer_assigned_to_the_order(OrderInterface $order): void
    {
        $order->getCustomer()->willReturn(null);

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_is_not_eligible_if_the_customer_has_no_birthday_date_set(
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $order->getCustomer()->willReturn($customer);
        $customer->getBirthday()->willReturn(null);

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_is_not_eligible_if_today_is_not_customer_birthday(
        ClockInterface $clock,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $clock->getCurrentDateTime()->willReturn(\DateTimeImmutable::createFromFormat('Y-m-d', '2020-01-05'));

        $order->getCustomer()->willReturn($customer);
        $customer->getBirthday()->willReturn(\DateTimeImmutable::createFromFormat('Y-m-d', '1980-05-14'));

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_is_eligible_if_today_is_the_date_of_customers_birthday(
        ClockInterface $clock,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $clock->getCurrentDateTime()->willReturn(\DateTimeImmutable::createFromFormat('Y-m-d', '2020-05-14'));

        $order->getCustomer()->willReturn($customer);
        $customer->getBirthday()->willReturn(\DateTimeImmutable::createFromFormat('Y-m-d', '1980-05-14'));

        $this->isEligible($order, [])->shouldReturn(true);
    }
}
