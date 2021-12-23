<?php

namespace Khazl\Timer;

use Carbon\CarbonInterval;
use DateInterval;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateIntervalCast implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        try {
            return new DateInterval($value);
        } catch (Exception $e) {
            throw new Exception('Not a valid DateInterval string');
        }
    }

    public function set($model, string $key, $value, array $attributes)
    {
        try {
            $value = is_string($value) ? CarbonInterval::create($value) : $value;

            return [$key => CarbonInterval::getDateIntervalSpec($value)];
        } catch (Exception $e) {
            throw new Exception('Cant extract DateInterval string');
        }
    }
}
