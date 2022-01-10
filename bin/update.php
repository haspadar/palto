#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

require_once 'safe_require_composer.php';

Backup::createArchive();
Update::run();