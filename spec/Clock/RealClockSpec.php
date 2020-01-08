<?php

declare(strict_types=1);

namespace spec\App\Clock;

use App\Clock\ClockInterface;
use App\Clock\RealClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class RealClockSpec extends ObjectBehavior
{
    function it_is_a_clock(): void
    {
        $this->shouldImplement(ClockInterface::class);
    }
}
