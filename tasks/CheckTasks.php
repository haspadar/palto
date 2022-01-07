<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run('composer update && vendor/bin/phinx migrate');
$task
    ->description('Composer update && Phinx migrate')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;