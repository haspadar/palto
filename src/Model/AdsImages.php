<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class AdsImages extends Model
{
    public static function getAdsImages(array $adIds): array
    {
        return self::getDb()->query('SELECT ad_id, big, small FROM ads_images WHERE ad_id IN %ld', $adIds);
    }

    public static function add($images): int
    {
        self::getDb()->insert('ads_images', $images);

        return self::getDb()->insertId();
    }
}