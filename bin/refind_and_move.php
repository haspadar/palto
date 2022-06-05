#!/usr/bin/php
<?php

use Palto\Logger;

require_once __DIR__ . '/autoload_require_composer.php';

Logger::info('Refind and move started');
\Palto\Synonyms::findAndMoveAds();
