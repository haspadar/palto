<?php

use Crunz\Schedule;

$schedule = new Schedule();

$task = $schedule->run('vendor/bin/phinx migrate');
$task
    ->everyFiveMinutes()
    ->description('Run phinx migrations')
    ->preventOverlapping();;

return $schedule;