<?php

namespace Palto;

class Regions
{
    public static function getWithAdsRegions(?Region $parentRegion = null): array
    {
        $regions = \Palto\Model\Regions::getWithAdsRegions($parentRegion);

        return array_map(fn($region) => new Region($region), $regions);
    }


    public static function getRegionsByIds(array $ids): array
    {
        if ($ids) {
            $regions = \Palto\Model\Regions::getRegionsByIds($ids);

            return $regions;
        }

        return [];
    }

    public static function getById(int $regionId)
    {
        $region = \Palto\Model\Regions::getById($regionId);

        return new Region($region);
    }

    public static function getByUrl(string $regionUrl)
    {
        return $regionUrl
            ? new Region(\Palto\Model\Regions::getByUrl($regionUrl))
            : new Region([]);
    }

    private static function groupByField(array $unGrouped, string $field): array
    {
        $grouped = [];
        foreach ($unGrouped as $data) {
            $grouped[$data[$field]][] = $data;
        }

        return $grouped;
    }

    private static function getChildRegions(array $region): array
    {
        $childrenIds = [];
        $nextLevelRegionsIds = [$region['id']];
        while ($nextLevelRegionsIds = \Palto\Model\Regions::getChildRegionsIds($nextLevelRegionsIds)) {
            $childrenIds = array_merge($nextLevelRegionsIds, $childrenIds);
        }

        return $childrenIds ? \Palto\Model\Regions::getRegionsByIds($childrenIds) : [];
    }
}