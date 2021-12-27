<?php

namespace Palto;

class Ads
{
    const LIMIT = 30;

    public static function getById(int $adId)
    {
        return new Ad(
            \Palto\Model\Ads::getById($adId),
            \Palto\Model\Ads::getAdsImages([$adId]),
            \Palto\Model\Ads::getAdsDetails([$adId])
        );
    }

    public static function getRegionsAdsCount(array $regionsIds): int
    {
        return $regionsIds
            ? \Palto\Model\Ads::getRegionsAdsCount($regionsIds)
            : 0;
    }

    public static function getCategoriesAdsCount(array $categoriesIds): int
    {
        return $categoriesIds
            ? \Palto\Model\Ads::getCategoriesAdsCount($categoriesIds)
            : 0;
    }

    public static function getAds(?Region $region, ?Category $category, int $limit = self::LIMIT, int $offset = 0, int $excludeId = 0): array
    {
        $ads = Model\Ads::getAds(
            $region,
            $category,
            $limit,
            $offset,
            $excludeId
        );
        $adIds = array_column($ads, 'id');
        $images = Ads::getAdsImages($adIds);
        $details = self::getAdsDetails($adIds);

        return array_map(
            fn(array $ad) => new Ad($ad, $images[$ad['id']] ?? [], $details[$ad['id']] ?? []),
            $ads
        );
    }

    private static function getAdsImages(array $adIds): array
    {
        if ($adIds) {
            $images = \Palto\Model\Ads::getAdsImages($adIds);

            return self::groupByField($images, 'ad_id');
        }

        return [];
    }

    private static function getAdsDetails(array $adIds): array
    {
        if ($adIds) {
            $details = \Palto\Model\Ads::getAdsDetails($adIds);
            $groupedByAdId = self::groupByField($details, 'ad_id');
            $groupedWithDetails = [];
            foreach ($groupedByAdId as $adId => $adDetails) {
                $groupedWithDetails[$adId] = array_column(
                    $adDetails,
                    'value',
                    'field'
                );
            }

            return $groupedWithDetails;
        }

        return [];
    }

    private static function groupByField(array $unGrouped, string $field): array
    {
        $grouped = [];
        foreach ($unGrouped as $data) {
            $grouped[$data[$field]][] = $data;
        }

        return $grouped;
    }

    public static function markAsDelete(int $adId)
    {
        \Palto\Model\Ads::markAsDeleted($adId);
    }
}