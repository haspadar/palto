<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Ads extends Model
{
    public static function getById(int $adId): array
    {
        return self::getDb()->queryFirstRow('SELECT a.* FROM ads AS a WHERE a.id = %d', $adId) ?: [] ;
    }

    public static function getByUrl(Url $url): array
    {
        return self::getDb()->queryFirstRow('SELECT a.* FROM ads AS a WHERE a.url = %s', $url->getFull()) ?: [];
    }

    public static function getAds(
        ?Region $region,
        ?Category $category,
        int $limit,
        int $offset,
        int $excludeId,
        string $orderBy
    ): array
    {
        $query = 'SELECT a.* FROM ads AS a ';
        if ($region || $category) {
            $query .= ' INNER JOIN ads_links AS al ON a.id = al.ad_id WHERE';
            $values = [];
            if ($category) {
                $query .= ' al.category_id = %d_category_id AND al.category_level = %d_category_level';
                $values['category_id'] = $category->getId();
                $values['category_level'] = $category->getLevel();
            }

            if ($region && $region->getId()) {
                $query .= ' al.region_id = %d_region_id AND al.region_level = %d_region_level';
                $values['region_id'] = $region->getId();
                $values['region_level'] = $region->getLevel();
            }
        }

        if ($excludeId) {
            if (!$category && !$region) {
                $query .= ' WHERE';
            }

            $query .= ' a.id <> %d_exclude_id';
            $values['exclude_id'] = $excludeId;
        }

        $query .= ' ORDER BY ' . $orderBy . ' LIMIT %d_limit OFFSET %d_offset';
        $values['limit'] = $limit;
        $values['offset'] = $offset;

        return self::getDb()->query($query, $values);
    }

    public static function getRegionsAdsCount(array $regionsIds): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ads WHERE region_id IN %ld', $regionsIds);
    }

    public static function getCategoriesAdsCount(array $categoriesIds): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ads WHERE category_id IN %ld', $categoriesIds);
    }

    public static function getAdLastTime(): ?string
    {
        return self::getDb()->queryFirstField("SELECT MAX(create_time) FROM ads");
    }

    public static function markAsDeleted(int $adId)
    {
        self::getDb()->update('ads', [
            'deleted_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id = %d", $adId);
    }

    public static function add(array $ad): int
    {
        self::getDb()->insert('ads', $ad);

        return self::getDb()->insertId();
    }

    public static function getByDonorUrl(string $donorUrl)
    {
        return self::getDb()->queryFirstRow('SELECT a.* FROM ads AS a WHERE a.donor_url = %s', $donorUrl) ?: [] ;
    }
}