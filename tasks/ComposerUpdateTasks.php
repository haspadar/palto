<?php

use Crunz\Schedule;
use Palto\Search;

$schedule = new Schedule();
$task = $schedule->run('composer update');
$task
    ->everyThirtyMinutes()
    ->description('Composer update')
    ->preventOverlapping();

return $schedule;