#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!is_dir($autoloadFile)) {
    echo 'Run `composer update` first' . PHP_EOL;

    exit;
}

require_once $autoloadFile;

Backup::createArchive();
Update::run();