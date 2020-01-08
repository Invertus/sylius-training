<?php

declare(strict_types=1);

namespace App\Context;

use App\Clock\ClockInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class TimeBasedChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var ClockInterface
     */
    private $clock;

    public function __construct(ChannelRepositoryInterface $channelRepository, ClockInterface $clock)
    {
        $this->channelRepository = $channelRepository;
        $this->clock = $clock;
    }

    public function getChannel(): ChannelInterface
    {
        if (($this->clock->getMinute() % 2) === 0) {
            return $this->channelRepository->findOneByCode('FASHION_WEB');
        }

        return $this->channelRepository->findOneByCode('NIGHT');
    }
}
