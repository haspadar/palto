<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Region;

class Regions extends Model
{
    public static function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM regions WHERE id = %d', $id) ?: [];
    }

    public static function getByUrl(string $url): array
    {
        if ($url) {
            return self::getDb()->queryFirstRow('SELECT * FROM regions WHERE url = %s', $url) ?: [];
        }

        return [];
    }

    public static function getLeafs(int $limit): array
    {
        $query = "SELECT * FROM regions WHERE id NOT IN (SELECT parent_id FROM regions WHERE parent_id IS NOT NULL)";
        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return self::getDb()->query($query);
    }

    public static function getRegionsByIds(array $ids): array
    {
        return self::getDb()->query('SELECT * FROM regions WHERE id IN %ld', $ids);
    }

    public static function getChildRegionsIds(array $regionsIds): array
    {
        return self::getDb()->queryFirstColumn(
            'SELECT id FROM regions WHERE parent_id IN %ld',
            $regionsIds,
        );
    }

    public static function getWithAdsRegions(?Region $parentRegion, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        if (!$parentRegion || $parentRegion->getLevel() < 2) {
            $query = 'SELECT * FROM regions';
            $values = [];
            $level = $parentRegion && $parentRegion->getId() ? $parentRegion->getLevel() + 1 : 1;
            $adsFieldRegion = "region_level_{$level}_id";
            $query .= " WHERE level=$level AND id IN (SELECT DISTINCT $adsFieldRegion FROM ads)";
            if ($parentRegion) {
                $query .= ' AND parent_id = %d_parent_id';
                $values['parent_id'] = $parentRegion->getId();
            }

            if ($orderBy) {
                $query .= ' ORDER BY ' .$orderBy;
            }

            if ($limit) {
                $query .= ' LIMIT %d_limit OFFSET %d_offset';
                $values['limit'] = $limit;
                $values['offset'] = $offset;
            }

            return self::getDb()->query($query, $values);
        } else {
            return [];
        }
    }

    public static function getMaxTreeId(): int
    {
        return self::getDb()->queryFirstField('SELECT MAX(tree_id) FROM regions') ?: 0;
    }

    public static function add(array $region): int
    {
        self::getDb()->insert('regions', $region);

        return self::getDb()->insertId();
    }

    public static function getMaxLevel(): int
    {
        return self::getDb()->queryFirstField('SELECT MAX(level) FROM regions') ?: 0;
    }
}