<?php

use Crunz\Schedule;
use Palto\Directory;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Directory::PARSE_ADS_SCRIPT);
$task
    ->hourly()
    ->description('Ads parser')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;