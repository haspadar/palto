#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;

$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!is_dir($autoloadFile)) {
    echo 'Run `composer update` first' . PHP_EOL;

    exit;
}

require_once $autoloadFile;

Cli::checkSudo();
Install::run();