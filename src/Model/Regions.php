<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Region;

class Regions extends Model
{
    public static function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM regions WHERE id = %d', $id);
    }

    public static function getByUrl(string $url): array
    {
        if ($url) {
            return self::getDb()->queryFirstRow('SELECT * FROM regions WHERE url = %s', $url) ?: [];
        }

        return [];
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
        $query = 'SELECT * FROM regions';
        $values = [];
        $adsFieldRegion = 'region_level_' . ($parentRegion ? $parentRegion->getLevel() + 1 : 1) . '_id';
        $query .= " WHERE id IN (SELECT $adsFieldRegion FROM ads)";
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
    }
}