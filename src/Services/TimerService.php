<?php


namespace Khazl\Timer\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Khazl\Timer\Contracts\TimerServiceInterface;
use Khazl\Timer\Models\Timer;

class TimerService implements TimerServiceInterface
{
    public function getTimersByOwner(string $ownerType, string|int $ownerId, bool $onlyOpen = true): Collection
    {
        $timers = Timer::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId);

        if ($onlyOpen) {
            $timers = $timers->whereNotIn('status', [
                config('timer.status.done'),
                config('timer.status.canceled')
            ])
                ->orWhereNull('status');
        }

        return $timers->get();
    }

    public function createTimer(\DateTime $from, int $duration, string $ownerType, int $ownerId, array $payload = []): ?Timer
    {
        $timer = new Timer();

        $timer->from = $from;
        $timer->duration = $duration;
        $timer->owner_type = $ownerType;
        $timer->owner_id = $ownerId;
        $timer->payload = $payload;

        if ($this->validateTimer($timer)) {
            $timer->save();
            return $timer;
        } else {
            throw new Exception('Timer is not valid.');
        }
    }

    private function validateTimer(Timer $timer): bool
    {
        // TODO: Add validation rules for timers.
        return true;
    }

    public function getRemainingByTimer(Timer $timer): array
    {
        $finishDate = Carbon::create($timer->from)->addSeconds($timer->duration);

        return [
            'finish_at' => $finishDate->toDateTime(),
            'seconds' => Carbon::now()->diffInSeconds($finishDate, false)
        ];
    }

    public function calculateTimerStatusByTimer(Timer $timer): int
    {
        $remaining = $this->getRemainingByTimer($timer);

        // Timer is already handled.
        if ($timer->status === config('timer.status.done') || $timer->status === config('timer.status.canceled')) {
            return $timer->status;
        }

        if ($remaining['seconds'] < 0) {
            return config('timer.status.done');
        }

        if ($remaining['seconds'] > 0 && $remaining['seconds'] < $timer->duration) {
            return config('timer.status.running');
        }

        return config('timer.status.pending');
    }

    public function cancelTimerByTimer(Timer $timer): void
    {
        $timer->status = config('timer.status.canceled');
        $timer->save();
    }

    /*
    public function filterTimerByStatus(array $timers, string $status, string $mode = 'inclusive'): array
    {
        $result = [];
        foreach ($timers as $timer) {
            if ($mode === 'inclusive') {
                // If status of timer is the same like passed
                if ($timer->status === $status) {
                    $result[] = $timer;
                }
            } elseif ($mode === 'exclusive') {
                // If status of timer is NOT the same like passed
                if ($timer->status !== $status) {
                    $result[] = $timer;
                }
            }
        }
        return $result;
    }
    */
}
