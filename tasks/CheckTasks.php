<?php

use Crunz\Schedule;

$schedule = new Schedule();
$task = $schedule->run('composer update && vendor/bin/phinx migrate');
$task
    ->everyFiveMinutes()
    ->description('Composer update && Phinx migrate')
    ->preventOverlapping();

return $schedule;