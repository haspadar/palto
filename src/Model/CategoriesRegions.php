<?php

namespace Palto\Model;

class CategoriesRegions extends Model
{
    public static function add(int $categoryId, ?int $regionId)
    {
        $category = Categories::getById($categoryId);
        if ($regionId) {
            $region = Regions::getById($regionId);
            $count = self::getDb()->query(
                'SELECT COUNT(*) AS count FROM ads WHERE category_level_' . $category['level'] . '_id = %d AND region_level_' . $region['level'] . '_id = %d',
                $categoryId,
                $regionId
            )[0]['count'];
        } else {
            $count = self::getDb()->query('SELECT COUNT(*) AS count FROM ads WHERE category_level_' . $category['level'] . '_id = %d', $categoryId)[0]['count'];
        }

        self::getDb()->insertUpdate('categories_regions', [
            'category_id' => $categoryId,
            'region_id' => $regionId,
            'ads_count' => $count
        ]);
    }
}