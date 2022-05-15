<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class AdsDetails extends Model
{
    protected string $name = 'ads_details';

    public function getAdsDetails(array $adIds): array
    {
        return self::getDb()->query(
            'SELECT ad_id, field, value FROM details_fields AS df INNER JOIN ' . $this->name . ' AS dfv ON df.id = dfv.details_field_id WHERE ad_id IN %ld',
            $adIds
        ) ?: [];
    }
}