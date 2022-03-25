<?php

use Crunz\Schedule;
use Dotenv\Dotenv;
use Palto\Directory;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

\Palto\Logger::debug('Configs directory: ' . Directory::getConfigsDirectory());
$env = array_merge(
    Dotenv::createImmutable(Directory::getConfigsDirectory(), '.env')->load(),
    Dotenv::createImmutable(Directory::getConfigsDirectory(), '.pylesos')->load(),
    Dotenv::createImmutable(Directory::getConfigsDirectory(), '.layouts')->load(),
);
\Palto\Logger::debug($env);

$task = $schedule->run(PHP_BINARY . ' ' . Directory::getParseAdsScript());
$task
    ->hourly()
    ->description('Ads parser')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;