<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;

class Regions
{
    public static function getRegions(): array
    {
        $regions = (new Model\Regions)->getRegions();

        return array_map(fn($region) => new Region($region), $regions);
    }

    public static function getLiveRegions(?Region $parentRegion = null, $limit = 0): array
    {
        $regions = (new Model\Regions)->getLiveRegions($parentRegion, $limit);

        return array_map(fn($region) => new Region($region), $regions);
    }

    /**
     * @param int $id
     * @return Region[]
     */
    public static function getParents(int $id): array
    {
        return array_map(
            fn(array $region) => new Region($region),
            (new \Palto\Model\Regions())->getParents($id)
        );
    }

    /**
     * @param int $limit
     * @return Region[]
     */
    public static function getLeafs(int $limit = 0): array
    {
        return array_map(
            fn($region) => new Region($region),
            (new Model\Regions)->getLeafs($limit)
        );
    }

    public static function getRegionsByIds(array $ids): array
    {
        if ($ids) {
            return (new Model\Regions)->getByIds($ids);
        }

        return [];
    }

    public static function getById(int $regionId): Region
    {
        $region = (new Model\Regions)->getById($regionId);

        return new Region($region);
    }

    public static function getByTitle(string $title, int $level = 0): ?Region
    {
        $region = (new Model\Regions)->getByTitleLevel($title, $level);

        return $region ? new Region($region) : null;
    }

    public static function getByDonorUrl(string $url, int $level = 0): ?Region
    {
        $found = (new Model\Regions)->getByDonorUrl($url, $level);

        return $found ? new Region($found) : null;
    }

    public static function getByUrl(string $regionUrl): ?Region
    {
        if (!$regionUrl || $regionUrl == Config::get('DEFAULT_REGION_URL')) {
            return new Region([]);
        } elseif ($found = (new Model\Regions)->getByUrl($regionUrl)) {
            return new Region($found);
        } else {
            return null;
        }
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

        if ($found = self::getByTitle($region['title'], $region['level'])) {
            return $found;
        }

        $region['url'] = self::generateUrl(($region['abbreviation'] ?? '') ?: $region['title'], true);
        $id = (new Model\Regions)->add($region);
        self::rebuildTree();
        Levels::checkRegionsFields();

        return new Region((new Model\Regions)->getById($id));
    }

    public static function getMaxLevel(): int
    {
        return (new \Palto\Model\Regions)->getMaxLevel();
    }

    public static function generateUrl(string $title, bool $addSuffix = false): string
    {
        $urlPattern = (new Slugify())->slugify($title);
        $url = $urlPattern;
        $counter = 0;
        if ($addSuffix) {
            while ((new Model\Regions)->getByUrl($url)) {
                $url = $urlPattern . '-' . (++$counter);
            }
        }

        return $url;
    }

    public static function getMaxTreeId(): int
    {
        return (new Model\Regions)->getMaxTreeId();
    }

    public static function rebuildAdsCount()
    {
        (new \Palto\Model\Regions())->rebuildAdsCount();
    }

    public static function rebuildTree()
    {
        (new \Palto\Model\Regions())->rebuildTree();
    }

    public static function addAd(Region $region)
    {
        (new \Palto\Model\Regions())->addAd($region->toArray());
    }

    public static function removeAd(Region $region)
    {
        (new \Palto\Model\Regions())->removeAd($region->toArray());
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
        while ($nextLevelRegionsIds = (new Model\Regions)->getChildRegionsIds($nextLevelRegionsIds)) {
            $childrenIds = array_merge($nextLevelRegionsIds, $childrenIds);
        }

        return $childrenIds ? (new Model\Regions)->getByIds($childrenIds) : [];
    }
}