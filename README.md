# Timer

## Installation

Get the package:
```bash
$ composer require khazl/timer
```

Install the package in your laravel app:
```bash
$ php artisan timer:install
$ php artisan migrate
```

Run sanity commands:
```bash
$ php artisan timer:update
$ php artisan timer:clear
```
It's highly recommended adding this command to your scheduler.  
- Once per day: `timer:clear`
- Often as possible: `timer:update`  

**IMPORTANT:** Timers can be done, also if they are not already flagged as done in the database.
Always calculate the status of a timer on runtime. Use the method `calculateTimerStatusByTimer` for this.

## Usage

You can use this lib via dependency injection or his facade.

```php
public function DepInj(TimerServiceInterface $timerService)
{
    $timerService->createTimer(...);
}

// or

public function NoDepInj()
{
    Timer::createTimer(...);
}
```

### Create timer

```php
/*
 * - Creates a timer for user with id 1
 * - Times runs from now on for 60 seconds
 */
Timer::createTimer(new \DateTime(), '60', 'User', '1');
```

### Get timers by owner

```php
/*
 * Gets all timers for user with id 1 which are not flagged as done or canceled
 */
Timer::getTimersByOwner('User', '1');

/*
 * Gets all timers for user with id 1 ALSO the ones flagged as done or canceled
 */
Timer::getTimersByOwner('User', '1', false);
```

Returns a `Collection` of `Timer`.

### Calculate the actual status of a timer

```php
$timers = Timer::getTimersByOwner('User', 1);
foreach ($timers as $timer) {
    dump(Timer::calculateTimerStatusByTimer($timer));
}
```

### Calculate the remaining time of a timer

```php
$timers = Timer::getTimersByOwner('User', 1);
foreach ($timers as $timer) {
    dump(Timer::getRemainingByTimer($timer));
}
```

Example return: 
```
[
  "finish_at" => \DateTime,
  "seconds" => 59
]
```

### Cancel one specific timer

```php
$timers = Timer::getTimersByOwner('User', 1);
Timer::cancelTimerByTimer($timers[0]);
```
