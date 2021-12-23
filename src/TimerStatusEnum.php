<?php

namespace Khazl\Timer;

class TimerStatusEnum
{
    public const Pending = 1;
    public const Running = 2;
    public const Done = 8;
    public const Canceled = 9;

    public const AlreadyHandled = [self::Done, self::Canceled];
}
