#!/usr/bin/php
<?php

use Palto\Levels;

require_once __DIR__ . '/autoload_require_composer.php';

Levels::checkRegionsFields();
Levels::checkCategoriesFields();
Levels::updateRegionsLevels();
Levels::updateCategoryLevels();