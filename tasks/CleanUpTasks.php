<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . \Palto\Ads::CLEAN_UP_SCRIPT);
$task
    ->daily()
    ->description('Clean up old ads')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;