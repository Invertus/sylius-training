<?php

declare(strict_types=1);

namespace App\Promotion;

use App\Clock\ClockInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class BirthdayRuleChecker implements RuleCheckerInterface
{
    /**
     * @var ClockInterface
     */
    private $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function isEligible(PromotionSubjectInterface $order, array $configuration): bool
    {
        if (!$order instanceof OrderInterface) {
            return false;
        }

        $customer = $order->getCustomer();

        if ($customer === null) {
            return false;
        }

        $birthday = $customer->getBirthday();

        if ($birthday === null) {
            return false;
        }

        return $birthday->format('d-m') === $this->clock->getCurrentDateTime()->format('d-m');
    }
}
