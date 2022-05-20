<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Ads extends Model
{
    protected string $name = 'ads';

    public static function getOldCount(): int
    {
        return self::getDb()->queryFirstField(
            'SELECT COUNT(*) FROM ads WHERE create_time < %s OR create_time IS NULL',
            (new \DateTime())->modify('-1 YEAR')->format('Y-m-d H:i:s')
        ) ?: 0;
    }

    public static function getOldAll(int $limit, int $offset): array
    {
        return self::getDb()->query(
            'SELECT * FROM ads WHERE create_time < %s OR create_time IS NULL LIMIT %d OFFSET %d',
            (new \DateTime())->modify('-1 YEAR')->format('Y-m-d H:i:s'),
            $limit,
            $offset
        ) ?: [];
    }

    public function getById(int $adId): array
    {
        $query = $this->getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.id = %d', $adId) ?: [] ;
    }

    public function getByUrl(Url $url): array
    {
        $query = $this->getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.url = %s', $url->getFull()) ?: [];
    }

    public function getAds(
        ?Region $region,
        ?Category $category,
        int $limit,
        int $offset,
        int $excludeId,
        string $orderBy
    ): array
    {
        $query = $this->getAdsQuery();
        [$where, $values] = $this->getAdsWhere($region, $category, $excludeId);
        $query .= $where;
        $query .= ' ORDER BY ' . $orderBy . ' LIMIT %d_limit OFFSET %d_offset';
        $values['limit'] = $limit;
        $values['offset'] = $offset;

        return self::getDb()->query($query, $values);
    }

    public function getRegionsAdsCount(array $regionsIds): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name . ' WHERE region_id IN %ld', $regionsIds);
    }

    public function getCategoriesAdsCounts(array $categoriesIds, int $level = 0): array
    {
        $field = $level ? "category_level_{$level}_id" : 'category_id';
        $counts = self::getDb()->query("SELECT COUNT(*) AS count, $field FROM " . $this->name . " WHERE $field IN %ld GROUP BY $field", $categoriesIds);

        return array_column($counts, 'count', $field);
    }

    public function getAdsCount(array $categoriesIds): int
    {
        if ($categoriesIds) {
            return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name . ' WHERE category_id IN %ld', $categoriesIds);
        }

        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name);
    }

    /**
     * @return string
     */
    private function getAdsQuery(): string
    {
        return 'SELECT a.*, c.title AS category_title, c.parent_id AS category_parent_id, c.level AS category_level,'
            . ' c.url AS category_url, r.title AS region_title, r.parent_id AS parent_region_id,'
            . ' r.level AS region_level, r.url AS region_url'
            . ' FROM ' . $this->name . ' AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
    }

    public function getLastTime(): string
    {
        return self::getDb()->queryFirstField("SELECT MAX(create_time) FROM " . $this->name) ?: '';
    }

    private function getAdsWhere(?Region $region, ?Category $category, int $excludeId): array
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

    public function markAsDeleted(int $adId)
    {
        self::getDb()->update($this->name, [
            'deleted_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id = %d", $adId);
    }

    public function getByDonorUrl(string $donorUrl)
    {
        $query = $this->getAdsQuery();

        return self::getDb()->queryFirstRow($query . ' WHERE a.donor_url = %s', $donorUrl) ?: [] ;
    }

    public function getFields(array $categoriesIds, array $fields, int $limit, int $offset): array
    {
        return self::getDb()->query(
            "SELECT "
                . implode(',', $fields)
                . " FROM "
                . $this->name
                . " WHERE category_id IN %ld LIMIT %d OFFSET %d",
                $categoriesIds,
            $limit,
            $offset
        );
    }

    public function getPairsCount(): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(distinct category_id, region_id) FROM ' . $this->name);
    }

    public function getPairs(int $limit, int $offset): array
    {
        return self::getDb()->query('SELECT distinct category_id, region_id FROM ' . $this->name . ' LIMIT %d OFFSET %d', $limit, $offset);
    }
}