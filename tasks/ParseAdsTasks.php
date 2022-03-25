<?php

use Crunz\Schedule;
use Dotenv\Dotenv;
use Palto\Directory;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Directory::getParseAdsScript());
$task
    ->everyMinute()
    ->description('Ads parser')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;