<?php

use Crunz\Schedule;
use Palto\Palto;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Palto::PARSE_ADS_SCRIPT);
$task
    ->hourly()
    ->description('Ads parser')
    ->preventOverlapping();;

return $schedule;