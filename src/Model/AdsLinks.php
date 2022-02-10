<?php

namespace Palto\Model;

class AdsLinks extends Model
{
    public static function add(int $adId, array $categoryIds, array $regionIds)
    {
        if (!$regionIds) {
            $regionIds = [null];
        }

        foreach ($categoryIds as $categoryLevel => $categoryId) {
            foreach ($regionIds as $regionLevel => $regionId) {
                self::getDb()->insertIgnore('ads_links', [
                    'ad_id' => $adId,
                    'category_id' => $categoryId,
                    'category_level' => $categoryLevel,
                    'region_id' => $regionId,
                    'region_level' => $regionLevel
                ]);
            }
        }
    }
}