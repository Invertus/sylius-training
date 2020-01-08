<?php

declare(strict_types=1);

namespace spec\App\Context;

use App\Context\TimeBasedChannelContext;
use App\Clock\ClockInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class TimeBasedChannelContextSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ClockInterface $clock
    ): void {
        $this->beConstructedWith($channelRepository, $clock);
    }

    function it_is_a_channel_context(): void
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_returns_fashion_channel_if_the_current_minute_is_even(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ClockInterface $clock
    ): void {
        $clock->getMinute()->willReturn(8);

        $channelRepository->findOneByCode('FASHION_WEB')->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_returns_night_channel_if_the_current_minute_is_odd(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ClockInterface $clock
    ): void {
        $clock->getMinute()->willReturn(7);

        $channelRepository->findOneByCode('NIGHT')->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }
}
