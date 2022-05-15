#!/usr/bin/php
<?php

use Palto\Backup;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Backup started');
$backupName = Backup::createArchive();
