<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . \Palto\Categories::REBUILD_SCRIPT);
$task
    ->daily()
    ->description('Rebuild trees')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;