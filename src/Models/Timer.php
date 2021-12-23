<?php

namespace Khazl\Timer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Khazl\Timer\DateIntervalCast;

class Timer extends Model
{
    use HasFactory;

    protected $casts = [
        'payload' => 'array',
        'from' => 'datetime',
        'duration' => DateIntervalCast::class
    ];
}
