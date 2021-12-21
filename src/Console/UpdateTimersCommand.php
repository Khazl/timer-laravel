<?php

namespace Khazl\Timer\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Khazl\Timer\Models\Timer;

class UpdateTimersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timer:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update timers status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Updating Timers ...');

        $now = Carbon::now();

        $timers = Timer::where('from', '<', $now)
            ->where(function ($query) {
                $query->whereNotIn('status', [config('timer.status.done'), config('timer.status.canceled')])
                    ->orWhereNull('status');
            })
            ->get();

        $this->comment("Potential timers found: {$timers->count()}");

        $batch = [];
        foreach ($timers as $timer) {
            // Flag as done
            $doneDate = Carbon::create($timer->from)->addSeconds($timer->duration);
            if ($doneDate->lessThan($now)) {
                $batch[] = $timer->id;
            }
        }

        Timer::whereIn('id', $batch)
            ->update([
                'status' => config('timer.status.done')
            ]);

        $this->info('Timers updated: ' . count($batch));
    }
}
