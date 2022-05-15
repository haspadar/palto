#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Update started');
Backup::createArchive();
Update::run();