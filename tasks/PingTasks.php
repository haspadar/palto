<?php

use Crunz\Schedule;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ping.php');
$task
    ->description('Ping')
    ->preventOverlapping();

return $schedule;