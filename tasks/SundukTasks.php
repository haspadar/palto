<?php

use Crunz\Schedule;
use Palto\Backup;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run(function () {
    Backup::sendSundukArchive();
});
$task
    ->daily()
    ->description('Send backup to Sunduk')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;