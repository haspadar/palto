#!/usr/bin/php
<?php

use Palto\Backup;

require_once 'safe_require_composer.php';

$backupName = Backup::createArchive();