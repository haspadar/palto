<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class AdsDetails extends Model
{
    public static function getAdsDetails(array $adIds): array
    {
        return self::getConnection()->query(
            'SELECT ad_id, field, value FROM details_fields AS df INNER JOIN ads_details AS dfv ON df.id = dfv.details_field_id WHERE ad_id IN %ld',
            $adIds
        );
    }

    public static function add(array $details): int
    {
        self::getConnection()->insert('ads_details', $details);

        return self::getConnection()->insertId();
    }
}