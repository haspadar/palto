#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Logger;

require_once '../vendor/autoload.php';

$backupName = Backup::createArchive();
if ($backupName) {
    Logger::info('Backup ' . $backupName . ' created');
} else {
    Logger::error('Can\'t create archive');
}