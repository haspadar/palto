<?php

use Palto\Palto;
use Palto\Status;

$rootDirectory = require_once 'autoload.php';
$palto = new Palto($rootDirectory);
$parserPid = $palto->getParserPid();
echo json_encode([
    'disk_mysql_used' => Status::getDirectoryUsePercent(
        Status::getMySqlDirectory($palto->getDb())
    ),
    'disk_root_used' => Status::getDirectoryUsePercent('/'),
    'ad_last_time' => $palto->getAdLastTime(),
    'parser_pid' => $parserPid ?: '',
    'parser_elapsed_time' => $parserPid ? Status::getParserElapsedTime($parserPid) : ''
]);