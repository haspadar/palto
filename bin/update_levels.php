#!/usr/bin/php
<?php

use Palto\Levels;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Update levels started');

Levels::checkRegionsFields();
Levels::checkCategoriesFields();
Levels::updateRegionsLevels();
Levels::updateCategoryLevels();