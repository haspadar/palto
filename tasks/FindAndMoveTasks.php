<?php

use Crunz\Schedule;
use Palto\Sitemap;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . \Palto\Synonyms::FIND_AND_MOVE_SCRIPT);
$task
    ->hourly()
    ->description('Find and move undefined and')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;