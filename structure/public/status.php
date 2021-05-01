<?php

use Palto\Palto;
use Palto\Status;

require_once '../vendor/autoload.php';

$palto = new Palto();
$parserPid = Status::getParserPid('parse_ads.php');
echo json_encode([
    'disk_mysql_used' => Status::getDirectoryUsePercent(
        Status::getMySqlDirectory($palto->getDb())
    ),
    'disk_root_used' => Status::getDirectoryUsePercent('/'),
    'ad_last_time' => $palto->getAdLastTime(),
    'parser_pid' => $parserPid ?: '',
    'parser_elapsed_time' => $parserPid ? Status::getParserElapsedTime($parserPid) : ''
]);