<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run('composer update && git reset --hard && git pull && vendor/bin/phinx migrate');
$task
    ->description('composer update, git pull, phinx migrate')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;