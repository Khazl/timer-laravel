<?php

namespace Khazl\Timer\Facades;

use Illuminate\Support\Facades\Facade;

class Timer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'timer';
    }
}
