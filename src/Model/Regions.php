<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Logger;
use Palto\Region;

class Regions extends NestedSet
{
    protected string $name = 'regions';

    public function getChildRegionsIds(array $regionsIds): array
    {
        return self::getDb()->queryFirstColumn(
            'SELECT id FROM ' . $this->name . ' WHERE parent_id IN %ld',
            $regionsIds,
        );
    }

    public function getRegions(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name);
    }

    public function getLiveRegions(?Region $parentRegion, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        $query = 'SELECT * FROM ' . $this->name;
        $values = [];
        $level = $parentRegion ? $parentRegion->getLevel() + 1 : 1;
        $adsFieldRegion = "region_level_{$level}_id";
        $query .= " WHERE level=$level AND id IN (SELECT DISTINCT $adsFieldRegion FROM ads)";
        if ($parentRegion && $parentRegion->getId()) {
            $query .= ' AND parent_id = %d_parent_id';
            $values['parent_id'] = $parentRegion->getId();
        } elseif ($parentRegion) {
            $query .= ' AND parent_id IS NULL';
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

    public function rebuildAdsCount()
    {
        $limit = 1000;
        $offset = 0;
        self::getDb()->query("UPDATE regions SET ads_count = 0");
        while ($regions = self::getDb()->query("SELECT id,title,parent_id FROM "
            . $this->name
            . " WHERE id IN(SELECT DISTINCT region_id FROM ads) LIMIT %d OFFSET %d", $limit, $offset
        )) {
            foreach ($regions as $region) {
                $adsCount = self::getDb()->queryFirstField("SELECT COUNT(*) FROM ads WHERE region_id = %d", $region['id']);
                if ($adsCount > 0) {
                    Logger::info('Updating ads count for region "' . $region['title'] . " and parents");
                    $this->updateAdsCounts($adsCount, $region);
                }
            }

            $offset += $limit;
        }
    }

    public function getByTitleLevel(string $title, int $level = 0)
    {
        return ($level
            ? self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE title = %s AND level = %d', $title, $level)
            : self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE title = %s', $title)
        ) ?: [];
    }
}