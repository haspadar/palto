<?php

namespace Palto\Model;

use Palto\Debug;

class CategoriesRegionsWithAds extends Model
{
    public static function add(int $categoryId, ?int $regionId)
    {
        $category = Categories::getById($categoryId);
        if ($regionId) {
            $region = Regions::getById($regionId);
            $count = self::getConnection()->query(
                'SELECT COUNT(*) AS count FROM ads WHERE category_level_' . $category['level'] . '_id = %d AND region_level_' . $region['level'] . '_id = %d',
                $categoryId,
                $regionId
            )[0]['count'];
        } else {
            $count = self::getConnection()->query('SELECT COUNT(*) AS count FROM ads WHERE category_level_' . $category['level'] . '_id = %d')[0]['count'];
        }

        self::getConnection()->insertUpdate('categories_regions_with_ads', [
            'category_id' => $categoryId,
            'region_id' => $regionId,
            'ads_count' => $count
        ]);
    }
}