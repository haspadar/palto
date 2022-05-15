<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Region;

class Regions extends Tree
{
    protected string $name = 'regions';

    public function getChildRegionsIds(array $regionsIds): array
    {
        return self::getDb()->queryFirstColumn(
            'SELECT id FROM ' . $this->name . ' WHERE parent_id IN %ld',
            $regionsIds,
        );
    }

    public function getWithAdsRegions(?Region $parentRegion, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        if (!$parentRegion || $parentRegion->getLevel() < 2) {
            $query = 'SELECT * FROM ' . $this->name;
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
}