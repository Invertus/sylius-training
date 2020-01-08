<?php

declare(strict_types=1);

namespace App\Clock;

final class RealClock implements ClockInterface
{
    public function getMinute(): int
    {
        return (int) (new \DateTimeImmutable())->format('i');
    }
}
