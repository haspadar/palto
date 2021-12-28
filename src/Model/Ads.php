<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class Ads extends Model
{
    public static function getById(int $adId): ?array
    {
        $query = self::getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.id = %d', $adId);//        return self::addAdData($ad);
    }

    public static function getAds(?Region $region, ?Category $category, int $limit, int $offset = 0, int $excludeId = 0): array
    {
        $query = self::getAdsQuery();
        [$where, $values] = self::getAdsWhere($region, $category, $excludeId);
        $query .= $where;
        $query .= ' ORDER BY create_time DESC LIMIT %d_limit OFFSET %d_offset';
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

//    public static function getAdsRegions(array $regionIds): array
//    {
//        if ($regionIds) {
//            $regions = self::getDb()->query('SELECT * FROM regions WHERE id IN %ld', $regionIds);
//            $grouped = $regions ? self::groupByField($regions, 'id') : [];
//            $adsRegions = [];
//            foreach ($regionIds as $regionId) {
//                $adsRegions[$regionId] = isset($grouped[$regionId])
//                    ? $grouped[$regionId][0]
//                    : self::getDefaultRegion();
//            }
//
//            return $adsRegions;
//        }
//
//
//        return [];
//    }

    /**
     * @return string
     */
    private static function getAdsQuery(): string
    {
        return 'SELECT a.*, c.title AS category_title, c.parent_id AS category_parent_id, c.level AS category_level,'
            . ' c.url AS category_url, r.title AS region_title, r.parent_id AS parent_region_id,'
            . ' r.level AS region_level, r.url AS region_url'
            . ' FROM ads AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
    }

//    private static function addAdData(?array $ad): ?array
//    {
//        if ($ad) {
//            $ad['images'] = self::getAdsImages([$ad['id']])[$ad['id']] ?? [];
//            $ad['details'] = self::getAdsDetails([$ad['id']])[$ad['id']] ?? [];
////            $ad['region'] = self::getAdsRegions([$ad['region_id']])[$ad['region_id']];
//        }
//
//        return $ad;
//    }

    public static function getAdsImages(array $adIds): array
    {
        return self::getDb()->query('SELECT ad_id, big, small FROM ads_images WHERE ad_id IN %ld', $adIds);
    }

    public static function getAdsDetails(array $adIds): array
    {
        return self::getDb()->query(
            'SELECT ad_id, field, value FROM details_fields AS df INNER JOIN ads_details AS dfv ON df.id = dfv.details_field_id WHERE ad_id IN %ld',
            $adIds
        );
    }

    private static function getAdsWhere(?Region $region, ?Category $category, int $excludeId): array
    {
        $query = ' WHERE ';
        $values = [];
        $where = [];
        if ($category) {
            $where[] = 'a.category_level_' . $category->getLevel() . '_id = %d_category';
            $values['category'] = $category->getId();
        }

        if ($region && $region->getId()) {
            $where[] = 'a.region_level_' . $region->getLevel() . '_id = %d_region';
            $values['region'] = $region->getId();
        }

//        $where[] = 'a.deleted_time IS NULL';
        $values['exclude'] = $excludeId;
        $where[] = 'a.id <> %d_exclude';
        $query .= implode(' AND ', $where);

        return [$query, $values];
    }

    public static function markAsDeleted(int $adId)
    {
        self::getDb()->update('ads', [
            'deleted_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id = %d", $adId);
    }
}