<?php

namespace Khazl\Timer\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timer:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the package';

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
        $this->comment('Publishing config ...');
        $this->callSilent('vendor:publish', ['--tag' => 'timer.config']);

        $this->comment('Publishing migrations ...');
        $this->callSilent('vendor:publish', ['--tag' => 'timer.migrations']);

        $this->info('Installation done. Have fun!');
    }
}
