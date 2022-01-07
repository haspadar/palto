<?php

use Crunz\Schedule;
use Palto\Sitemap;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' ' . Sitemap::GENERATE_SCRIPT);
$task
    ->daily()
    ->description('Sitemap generator')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;