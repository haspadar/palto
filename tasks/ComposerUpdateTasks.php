<?php

use Crunz\Schedule;
use Palto\Search;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Search::REINDEX_SCRIPT);
$task
    ->everyFiveMinutes()
    ->description('Composer update')
    ->preventOverlapping();;

return $schedule;