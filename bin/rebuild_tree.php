#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Debug;
use Palto\Model\Categories;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Categories::rebuildTree();
\Palto\Regions::rebuildTree();