#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Install started');
Cli::checkSudo();
Install::run();