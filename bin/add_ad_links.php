#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

$adsCount = \Palto\Model\AdsLinks::getDb()->query("select count(*) as count from ads")[0]['count'];
$limit = 10000;
$offset = 0;
while ($ads = \Palto\Model\AdsLinks::getDb()->query("select a.id,category_id,region_id,c.level as category_level,c.parent_id as category_parent_id,r.level as region_level,r.parent_id as region_parent_id from ads as a inner join categories as c on a.category_id=c.id left join regions as r on a.region_id=r.id limit $limit offset $offset")) {
    echo 'Next ' . ($offset + count($ads)) . ' ads from ' . $adsCount . PHP_EOL;
    foreach ($ads as $ad) {
        $categoryIds = getCategoryIds($ad['category_id'], $ad['category_level']);
        $regionIds = getRegionIds($ad['region_id'], $ad['region_level']);
        \Palto\Model\AdsLinks::add($ad['id'], $categoryIds, $regionIds);
    }

    $offset += $limit;
}

function getCategoryIds($categoryId, $categoryLevel): array
{
    $ids = [];
    for ($level = $categoryLevel; $level >= 1; $level--) {
        $category = \Palto\Model\AdsLinks::getDb()->queryFirstRow("select id,parent_id,level from categories where id=$categoryId");
        $ids[$level] = $category['id'];
        $categoryId = $category['parent_id'];
    }

    return $ids;
}

function getRegionIds($regionId, $regionLevel): array
{
    $ids = [];
    for ($level = $regionLevel; $level >= 1; $level--) {
        $region = \Palto\Model\AdsLinks::getDb()->queryFirstRow("select id,parent_id,level from regions where id=$regionId");
        $ids[$level] = $region['id'];
        $regionId = $region['parent_id'];
    }

    return $ids;
}