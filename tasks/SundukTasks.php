<?php

use Crunz\Schedule;
use Palto\Backup;
use Palto\Config;
use Palto\Logger;

//require_once realpath(__DIR__ . '/../../../../public/autoload.php');
\Palto\Debug::dump(\Palto\Directory::getProjectName());exit;
$schedule = new Schedule();
$task = $schedule->run(function () {
    $backupName = Backup::createArchive(\Palto\Directory::getProjectName());
    if ($backupName) {
        Logger::info('Sunduk backup ' . $backupName . ' created');
    } else {
        Logger::error('Can\'t create Sunduk archive');
    }

    if (Config::get('SUNDUK_URL')) {
        $isSent = Backup::sendSundukArchive($backupName, Config::get('SUNDUK_URL'));
        if ($isSent) {
            Logger::info('Sent Sunduk archive successfully');
        } else {
            Logger::error('Can\'t send Sunduk archive');
        }

    } else {
        Logger::error('Can\'t send Sunduk archive – SUNDUK_URL is empty');
    }
});
$task
    ->daily()
    ->description('Send backup to Sunduk')
    ->preventOverlapping();

return $schedule;