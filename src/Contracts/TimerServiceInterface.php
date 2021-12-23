<?php

namespace Khazl\Timer\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Khazl\Timer\Models\Timer;

interface TimerServiceInterface
{
    public function getTimersByOwner(string $ownerType, string|int $ownerId, bool $onlyOpen = true): Collection;

    public function createTimer(\DateTime $from, \DateInterval $duration, string $ownerType, int $ownerId, array $payload = []): ?Timer;

    // public function filterTimerByStatus(array $timers, string $status, string $mode): array;

    public function getRemainingByTimer(Timer $timer): array;

    public function calculateTimerStatusByTimer(Timer $timer): int;

    public function cancelTimerByTimer(Timer $timer): void;

    public function updateTimerStatusByTimer(Timer $timer, bool $autoSave = false): Timer;
}
