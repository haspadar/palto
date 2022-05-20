<?php

use Palto\Status;

require_once '../vendor/autoload.php';
$parserPid = Status::getPhpCommandPid(\Palto\Directory::getParseAdsScript(), \Palto\Directory::getProjectName());
echo json_encode([
    'disk_mysql_used' => Status::getDirectoryUsePercent(
        Status::getMySqlDirectory(\Palto\Model\Ads::getDb())
    ) . '%',
    'disk_root_used' => Status::getDirectoryUsePercent('/') . '%',
    'ad_last_time' => \Palto\Ads::getLastTime(),
    'parser_pid' => $parserPid ?: '',
    'parser_elapsed_time' => $parserPid ? Status::getParserElapsedTime($parserPid) : ''
]);