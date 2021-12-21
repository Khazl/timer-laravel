<?php

namespace Khazl\Timer;

use Illuminate\Support\ServiceProvider;
use Khazl\Timer\Console\ClearTimersCommand;
use Khazl\Timer\Console\InstallCommand;
use Khazl\Timer\Console\UpdateTimersCommand;
use Khazl\Timer\Contracts\TimerServiceInterface;
use Khazl\Timer\Services\TimerService;

class TimerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/timer.php', 'timer');
        $this->app->bind(TimerServiceInterface::class, TimerService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['timer'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/timer.php' => config_path('timer.php'),
        ], 'timer.config');

        // Registering package commands.
        $this->commands([InstallCommand::class, ClearTimersCommand::class, UpdateTimersCommand::class]);
    }
}
