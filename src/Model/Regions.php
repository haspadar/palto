<?php

namespace Palto\Model;

use Palto\Debug;

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

    public static function getRegions(int $parentId, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        $query = 'SELECT * FROM regions';
        $values = [];
        if ($parentId) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d_parent_id';
            $values['parent_id'] = $parentId;
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