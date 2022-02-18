<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Region;

class Regions extends Model
{
    public static function getById(int $id): array
    {
        return self::getConnection()->queryFirstRow('SELECT * FROM regions WHERE id = %d', $id) ?: [];
    }

    public static function getByUrl(string $url): array
    {
        if ($url) {
            return self::getConnection()->queryFirstRow('SELECT * FROM regions WHERE url = %s', $url) ?: [];
        }

        return [];
    }

    public static function getRegionsByIds(array $ids): array
    {
        return self::getConnection()->query('SELECT * FROM regions WHERE id IN %ld', $ids);
    }

    public static function getChildRegionsIds(array $regionsIds): array
    {
        return self::getConnection()->queryFirstColumn(
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

            return self::getConnection()->query($query, $values);
        } else {
            return [];
        }
    }

    public static function getMaxTreeId(): int
    {
        return self::getConnection()->queryFirstField('SELECT MAX(tree_id) FROM regions') ?: 0;
    }

    public static function add(array $region): int
    {
        self::getConnection()->insert('regions', $region);

        return self::getConnection()->insertId();
    }

    public static function getMaxLevel(): int
    {
        return self::getConnection()->createQueryBuilder()
            ->select('MAX(level)')
            ->from('regions')
            ->fetchOne() ?: 0;
    }
}