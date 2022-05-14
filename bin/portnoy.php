#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;
use Palto\Portnoy;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Portnoy started');
Cli::checkSudo();
Portnoy::run();