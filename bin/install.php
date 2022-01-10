#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;

require_once __DIR__ . '/../vendor/autoload.php';

Cli::checkSudo();
Install::run();