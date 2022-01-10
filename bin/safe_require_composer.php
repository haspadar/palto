<?php
$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoloadFile)) {
    echo 'Run `composer update` first' . PHP_EOL;

    exit;
}

require_once $autoloadFile;