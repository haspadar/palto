<?php

use Crunz\Schedule;
use Palto\Backup;
use Palto\Palto;

$schedule = new Schedule();

$task = $schedule->run(function () {
    $palto = new Palto();
    $backupName = Backup::createSundukArchive($palto->getProjectName());
    $logger = $palto->getLogger();
    if ($backupName) {
        $logger->info('Sunduk backup ' . $backupName . ' created');
    } else {
        $logger->error('Can\'t create Sunduk archive');
    }

    if (isset($palto->getEnv()['SUNDUK_URL']) && $palto->getEnv()['SUNDUK_URL']) {
        $isSent = Backup::sendSundukArchive($backupName, $palto->getEnv()['SUNDUK_URL']);
        if ($isSent) {
            $logger->info('Sent Sunduk archive successfully');
        } else {
            $logger->error('Can\'t send Sunduk archive');
        }

    } else {
        $logger->error('Can\'t send Sunduk archive – SUNDUK_URL is empty');
    }
});
$task
    ->daily()
    ->description('Send backup to Sunduk')
    ->preventOverlapping();

return $schedule;