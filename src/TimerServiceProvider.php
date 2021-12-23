<?php

namespace Khazl\Timer;

use DateInterval;
use Illuminate\Support\Facades\Validator;
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

        Validator::extend('string_or_int', function ($attribute, $value) {
            return is_string($value) || is_integer($value);
        });

        Validator::extend('dateinterval', function ($attribute, $value) {
            return is_object($value) && get_class($value) === DateInterval::class;
        });

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

        // Register the service the package provides.
        $this->app->singleton('timer', function ($app) {
            return new TimerService();
        });
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

        // Publishing the migration files.
        $this->publishes([
            __DIR__.'/../database/migrations/2021_12_17_152634_create_timers_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_timers_table.php'),
        ], 'timer.migrations');

        // Registering package commands.
        $this->commands([InstallCommand::class, ClearTimersCommand::class, UpdateTimersCommand::class]);
    }
}
