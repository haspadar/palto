<?php

namespace Palto;

use Cocur\Slugify\Slugify;

class Regions
{
    public static function getWithAdsRegions(?Region $parentRegion, $limit): array
    {
        $regions = \Palto\Model\Regions::getWithAdsRegions($parentRegion, $limit);

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

    public static function getById(int $regionId): Region
    {
        $region = \Palto\Model\Regions::getById($regionId);

        return new Region($region);
    }

    public static function getByUrl(string $regionUrl): Region
    {
        return $regionUrl
            ? new Region(\Palto\Model\Regions::getByUrl($regionUrl))
            : new Region([]);
    }

    public static function safeAdd(array $region): Region
    {
        $region['create_time'] = (new \DateTime())->format('Y-m-d H:i:s');
        if (!isset($region['parent_id']) || !$region['parent_id']) {
            $region['level'] = 1;
            $region['tree_id'] = self::getMaxTreeId() + 1;
        } else {
            $parent = self::getById($region['parent_id']);
            $region['level'] = $parent->getLevel() + 1;
            $region['tree_id'] = $parent->getTreeId();
        }

        $id = \Palto\Model\Regions::add($region);

        return new Region(\Palto\Model\Regions::getById($id));
    }

    public static function generateUrl(string $title): string
    {
        $urlPattern = (new Slugify())->slugify($title);
        $url = $urlPattern;
        $counter = 0;
        while (\Palto\Model\Regions::getByUrl($url)) {
            $url = $urlPattern . '-' . (++$counter);
        }

        return $url;
    }

    public static function getMaxTreeId(): int
    {
        return \Palto\Model\Regions::getMaxTreeId();
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