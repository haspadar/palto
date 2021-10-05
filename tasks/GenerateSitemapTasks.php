<?php

use Crunz\Schedule;
use Palto\Sitemap;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Sitemap::GENERATE_SCRIPT);
$task
    ->daily()
    ->description('Sitemap generator')
    ->preventOverlapping();;

return $schedule;