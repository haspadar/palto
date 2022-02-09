<?php

namespace Palto\Model;

class CategoriesRegionsWithAds extends Model
{
    public static function add(int $categoryId, ?int $regionId)
    {
        $category = Categories::getById($categoryId);
        if ($regionId) {
            $region = Regions::getById($regionId);
            $count = self::getDb()->query(
                'SELECT COUNT(*) FROM ads WHERE category_level_' . $category['level'] . '_id = %d AND region_level_' . $region['level'] . '_id = %d',
                $categoryId,
                $regionId
            );
        } else {
            $count = self::getDb()->query('SELECT COUNT(*) FROM ads WHERE category_level_' . $category['level'] . '_id = %d');
        }

        self::getDb()->insertUpdate('categories_regions_with_ads', [
            'category_id' => $categoryId,
            'region_id' => $regionId,
            'ads_count' => $count
        ]);
    }
}