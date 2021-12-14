<?php

use Crunz\Schedule;
use Palto\Search;
use Palto\Sphinx;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Sphinx::REINDEX_SCRIPT);
$task
    ->everyThirtyMinutes()
    ->description('Update sphinx index')
    ->preventOverlapping();

return $schedule;