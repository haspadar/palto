<?php

use Crunz\Schedule;
use Palto\Backup;
use Palto\Palto;

$schedule = new Schedule();
$task = $schedule->run(function () {

    $logger = (new Palto())->getLogger();
    $backupName = Backup::createConfigArchive();
    if ($backupName) {
        $logger->info('Env backup ' . $backupName . ' created');
    } else {
        $logger->error('Can\'t create config archive');
    }
});
$task
    ->daily()
    ->description('Main config local backup')
    ->preventOverlapping();

return $schedule;