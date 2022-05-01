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
        $query = self::getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.id = %d', $adId) ?: [] ;
    }

    public static function getByUrl(Url $url): array
    {
        $query = self::getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.url = %s', $url->getFull()) ?: [];
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
        $query = self::getAdsQuery();
        [$where, $values] = self::getAdsWhere($region, $category, $excludeId);
        $query .= $where;
        $query .= ' ORDER BY ' . $orderBy . ' LIMIT %d_limit OFFSET %d_offset';
        $values['limit'] = $limit;
        $values['offset'] = $offset;

        return self::getDb()->query($query, $values);
    }

    public static function getRegionsAdsCount(array $regionsIds): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ads WHERE region_id IN %ld', $regionsIds);
    }

    public static function getCategoriesAdsCounts(array $categoriesIds, int $level = 0): array
    {
        $field = $level ? "category_level_{$level}_id" : 'category_id';
        $counts = self::getDb()->query("SELECT COUNT(*) AS count, $field FROM ads WHERE $field IN %ld GROUP BY $field", $categoriesIds);

        return array_column($counts, 'count', $field);
    }

    public static function getCategoriesAdsCount(array $categoriesIds): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ads WHERE category_id IN %ld', $categoriesIds);
    }

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

    public static function getAdLastTime(): ?string
    {
        return self::getDb()->queryFirstField("SELECT MAX(create_time) FROM ads");
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

    public static function add(array $ad): int
    {
        self::getDb()->insert('ads', $ad);

        return self::getDb()->insertId();
    }

    public static function getByDonorUrl(string $donorUrl)
    {
        $query = self::getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.donor_url = %s', $donorUrl) ?: [] ;
    }

    public static function update(array $updates, int $id)
    {
        self::getDb()->update('ads', $updates, 'id = %d', $id);
    }
}