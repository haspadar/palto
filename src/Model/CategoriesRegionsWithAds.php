<?php

namespace Palto\Model;

class CategoriesRegionsWithAds extends Model
{
    public static function add(int $categoryId, ?int $regionId)
    {
        self::getDb()->insertIgnore('categories_regions_with_ads', [
            'category_id' => $categoryId,
            'region_id' => $regionId
        ]);
    }
}