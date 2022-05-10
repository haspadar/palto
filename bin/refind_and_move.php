#!/usr/bin/php
<?php

use Palto\Categories;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Synonyms::findAndMoveAds(Categories::getLiveCategories());
