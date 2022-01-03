#!/usr/bin/php
<?php

use Palto\Backup;

require_once __DIR__ . '/../../../autoload.php';

$backupName = Backup::createArchive();