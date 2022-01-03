<?php

use Crunz\Schedule;
use Palto\Directory;
use Palto\Palto;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Directory::PARSE_ADS_SCRIPT);
$task
    ->hourly()
    ->description('Ads parser')
    ->preventOverlapping();

return $schedule;