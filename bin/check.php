#!/usr/bin/php
<?php

use Palto\Debug;
use Palto\Email;
use Palto\Logger;

require_once __DIR__ . '/../vendor/autoload.php';

if (isset($argv['1'])) {
    \Palto\Directory::getConfigsDirectory();
    $command = \Palto\Directory::getRootDirectory()
        . '/vendor/bin/phpunit '
        . \Palto\Directory::getTestsDirectory();
    Debug::dump($command);
    $response = `$command`;

    $responseRows = array_values(array_filter(explode(PHP_EOL, $response)));
    $responseLastRow = $responseRows[count($responseRows) - 1];
    $isSuccess = mb_substr($responseLastRow, 0, 2) == 'OK';
    if (!$isSuccess) {
        Email::send($argv['1'], 'Ошибка на ' . \Palto\Directory::getProjectName(), $response);
        Logger::error($response);
    } else {
        Logger::info($responseLastRow);
    }
} else {
    Logger::error('Первый параметр – почта для ошибок');
}