<?php

namespace Khazl\Timer\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Khazl\Timer\Models\Timer;
use Khazl\Timer\TimerStatusEnum;

class ClearTimersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timer:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears old timers';

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
        $this->comment('Clearing Timers ...');

        if (!config('timer.delete_after_seconds')) {
            $this->error('Please set a deletion time threshold in your config. Check docu for help.');
            return 1;
        }

        $thresholdDate = Carbon::now()->subSeconds(config('timer.delete_after_seconds'));

        $this->comment("Deleting timers which are done or canceled and not updated since {$thresholdDate}");

        $deleted = Timer::where('updated_at', '<', $thresholdDate)
            ->whereIn('status', TimerStatusEnum::AlreadyHandled)
            ->delete();

        $this->info("Timers deleted: {$deleted}");
    }
}
