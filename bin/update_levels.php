#!/usr/bin/php
<?php

use Palto\Levels;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Update levels started');

Levels::checkRegionsFields();
Levels::checkCategoriesFields();
Levels::updateRegionsLevels();
Levels::updateCategoryLevels();


/**
 * Запросы для обновления region_level:
 * UPDATE ads AS a INNER JOIN regions AS r ON a.region_id = r.id SET a.region_level_1_id = r.id, a.region_level_2_id=null, a.region_level_3_id=null WHERE r.level=1;
 * UPDATE ads AS a INNER JOIN regions AS r ON a.region_id = r.id SET a.region_level_2_id = r.id, a.region_level_1_id=r.parent_id, a.region_level_3_id=null WHERE r.level=2;
 * UPDATE ads AS a INNER JOIN regions AS r ON a.region_id = r.id INNER JOIN regions AS rr ON r.parent_id=rr.id SET a.region_level_3_id = r.id, a.region_level_2_id=r.parent_id, a.region_level_1_id=rr.parent_id WHERE r.level=3;
 */