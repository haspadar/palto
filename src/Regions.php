<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;

class Regions
{
    public static function getWithAdsRegions(?Region $parentRegion, $limit): array
    {
        $regions = Model\Regions::getWithAdsRegions($parentRegion, $limit);

        return array_map(fn($region) => new Region($region), $regions);
    }


    public static function getRegionsByIds(array $ids): array
    {
        if ($ids) {
            $regions = Model\Regions::getRegionsByIds($ids);

            return $regions;
        }

        return [];
    }

    public static function getById(int $regionId): Region
    {
        $region = Model\Regions::getById($regionId);

        return new Region($region);
    }

    public static function getByUrl(string $regionUrl): Region
    {
        return $regionUrl
            ? new Region(Model\Regions::getByUrl($regionUrl))
            : new Region([]);
    }

    public static function safeAdd(array $region): Region
    {
        $region['create_time'] = (new DateTime())->format('Y-m-d H:i:s');
        if (!isset($region['parent_id']) || !$region['parent_id']) {
            $region['level'] = 1;
            $region['tree_id'] = self::getMaxTreeId() + 1;
        } else {
            $parent = self::getById($region['parent_id']);
            $region['level'] = $parent->getLevel() + 1;
            $region['tree_id'] = $parent->getTreeId();
        }

        $found = self::getByUrl($region['url']);
        if ($found && $found->getId()) {
            return $found;
        }

        $id = Model\Regions::add($region);

        return new Region(Model\Regions::getById($id));
    }

    public static function generateUrl(string $title): string
    {
        $urlPattern = (new Slugify())->slugify($title);
        $url = $urlPattern;
        $counter = 0;
        while (Model\Regions::getByUrl($url)) {
            $url = $urlPattern . '-' . (++$counter);
        }

        return $url;
    }

    public static function getMaxTreeId(): int
    {
        return Model\Regions::getMaxTreeId();
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
        while ($nextLevelRegionsIds = Model\Regions::getChildRegionsIds($nextLevelRegionsIds)) {
            $childrenIds = array_merge($nextLevelRegionsIds, $childrenIds);
        }

        return $childrenIds ? Model\Regions::getRegionsByIds($childrenIds) : [];
    }
}