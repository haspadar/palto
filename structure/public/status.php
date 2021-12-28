<?php

use Palto\Palto;
use Palto\Status;

require_once '../vendor/autoload.php';
$parserPid = Status::getPhpCommandPid(\Palto\Directory::PARSE_ADS_SCRIPT, \Palto\Directory::getProjectName());
echo json_encode([
    'disk_mysql_used' => Status::getDirectoryUsePercent(
        Status::getMySqlDirectory(\Palto\Model\Ads::getDb())
    ),
    'disk_root_used' => Status::getDirectoryUsePercent('/'),
    'ad_last_time' => \Palto\Model\Ads::getAdLastTime(),
    'parser_pid' => $parserPid ?: '',
    'parser_elapsed_time' => $parserPid ? Status::getParserElapsedTime($parserPid) : ''
]);