<?php


namespace Khazl\Timer\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Khazl\Timer\Contracts\TimerServiceInterface;
use Khazl\Timer\Models\Timer;
use Khazl\Timer\TimerStatusEnum;

class TimerService implements TimerServiceInterface
{
    public function getTimersByOwner(string $ownerType, string|int $ownerId, bool $onlyOpen = true): Collection
    {
        $timers = Timer::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId);

        if ($onlyOpen) {
            $timers = $timers->whereNotIn('status', [
                TimerStatusEnum::Done,
                TimerStatusEnum::Canceled
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
        $timer = $timer->toArray();

        $validator = Validator::make($timer, [
            'from' => ['required', 'date'],
            'duration' => ['required', 'numeric', 'min:1'],
            'owner_type' => ['required', 'string'],
            'owner_id' => ['required', 'string_or_int'],
            'payload' => ['array'],
        ]);

        return !$validator->fails();
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
        if ($timer->status === TimerStatusEnum::Done || $timer->status === TimerStatusEnum::Canceled) {
            return $timer->status;
        }

        if ($remaining['seconds'] < 0) {
            return TimerStatusEnum::Done;
        }

        if ($remaining['seconds'] > 0 && $remaining['seconds'] < $timer->duration) {
            return TimerStatusEnum::Running;
        }

        return TimerStatusEnum::Pending;
    }

    public function cancelTimerByTimer(Timer $timer): void
    {
        $timer->status = TimerStatusEnum::Canceled;
        $timer->save();
    }

    public function updateTimerStatusByTimer(Timer $timer, bool $autoSave = false): Timer
    {
        $timer->status = $this->calculateTimerStatusByTimer($timer);

        if ($autoSave && $timer->isDirty()) {
            $timer->save();
        }

        return $timer;
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
