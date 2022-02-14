#!/usr/bin/php
<?php

use Palto\Links;

require_once __DIR__ . '/autoload_require_composer.php';

Links::updateCategoryLinks();
Links::updateRegionLinks();