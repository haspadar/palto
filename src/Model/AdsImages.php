<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class AdsImages extends Model
{
    protected string $name = 'ads_images';

    public function getAdsImages(array $adIds): array
    {
        return self::getDb()->query('SELECT ad_id, big, small FROM ' . $this->name . ' WHERE ad_id IN %ld', $adIds) ?: [];
    }
}