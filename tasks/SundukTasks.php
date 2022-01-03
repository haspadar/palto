<?php

use Crunz\Schedule;
use Palto\Backup;

$schedule = new Schedule();
$task = $schedule->run(function () {
    Backup::sendSundukArchive();
});
$task
    ->daily()
    ->description('Send backup to Sunduk')
    ->preventOverlapping();

return $schedule;