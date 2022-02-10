<?php

namespace Palto;

use DateTime;
use Palto\Model\AdsDetails;
use Palto\Model\AdsImages;
use Palto\Model\AdsLinks;
use Palto\Model\DetailsFields;

class Ads
{
    const LIMIT = 30;

    public static function getById(int $adId): ?Ad
    {
        $row = Model\Ads::getById($adId);

        return $row
            ? new Ad($row, AdsImages::getAdsImages([$adId]), AdsDetails::getAdsDetails([$adId]))
            : null;
    }

    public static function getByUrl(Url $adUrl): ?Ad
    {
        $row = Model\Ads::getByUrl($adUrl);

        return $row
            ? new Ad($row, AdsImages::getAdsImages([$row['id']]), AdsDetails::getAdsDetails([$row['id']]))
            : null;
    }

    public static function getRegionsAdsCount(array $regionsIds): int
    {
        return $regionsIds
            ? Model\Ads::getRegionsAdsCount($regionsIds)
            : 0;
    }

    public static function getCategoriesAdsCount(array $categoriesIds): int
    {
        return $categoriesIds
            ? Model\Ads::getCategoriesAdsCount($categoriesIds)
            : 0;
    }

    public static function getAds(
        ?Region $region,
        ?Category $category,
        int $limit = self::LIMIT,
        int $offset = 0,
        int $excludeId = 0,
        string $orderBy = 'create_time DESC'
    ): array
    {
        $ads = Model\Ads::getAds(
            $region,
            $category,
            $limit,
            $offset,
            $excludeId,
            $orderBy
        );
        $adIds = array_column($ads, 'id');
        $images = Ads::getAdsImages($adIds);
        $details = self::getAdsDetails($adIds);

        return array_map(
            fn(array $ad) => new Ad($ad, $images[$ad['id']] ?? [], $details[$ad['id']] ?? []),
            $ads
        );
    }

    public static function markAsDelete(int $adId)
    {
        Model\Ads::markAsDeleted($adId);
    }

    public static function add(array $ad, array $images = [], array $details = []): int
    {
        if (isset($ad['donor_url']) && ($found = self::getByDonorUrl($ad['donor_url']))) {
            return $found->getId();
        } elseif ($ad['title'] && $ad['text']) {
            $ad['title'] = Filter::get($ad['title']);
            $ad['text'] = Filter::get($ad['text']);
            $ad['create_time'] = (new DateTime())->format('Y-m-d H:i:s');
            try {
                $adId = Model\Ads::add($ad);
                CategoriesRegionsWithAds::add($ad['category_id'], $ad['region_id']);
                self::addLinks($ad);
            } catch (\Exception $e) {
                Logger::error(var_export($ad, true));
                Logger::error($e->getTraceAsString());

                return 0;
            }

            foreach ($images as $image) {
                if ($image['small'] || $image['big']) {
                    AdsImages::add([
                        'small' => $image['small'],
                        'big' => $image['big'],
                        'ad_id' => $adId,
                    ]);
                }
            }

            foreach ($details as $detailField => $detailValue) {
                if ($detailField && $detailValue) {
                    $fieldId = DetailsFields::getDetailsFieldId($ad['category_id'], $detailField);
                    try {
                        AdsDetails::add([
                            'details_field_id' => $fieldId,
                            'ad_id' => $adId,
                            'value' => Filter::get($detailValue)
                        ]);
                    } catch (\Exception $e) {
                        Logger::error(var_export($ad, true));
                        Logger::error($e->getTraceAsString());

                        return $adId;
                    }
                }
            }

            return $adId;
        } else {
            Logger::debug('Ignored ad ' . $ad['url'] . ': empty ' . (!$ad['title'] ? 'title' : 'text'));

            return 0;
        }
    }

    private static function getAdsImages(array $adIds): array
    {
        if ($adIds) {
            $images = AdsImages::getAdsImages($adIds);

            return self::groupByField($images, 'ad_id');
        }

        return [];
    }

    private static function getAdsDetails(array $adIds): array
    {
        if ($adIds) {
            $details = AdsDetails::getAdsDetails($adIds);
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

    private static function addLinks(array $ad)
    {
        $categoryIds = [];
        if (isset($ad['category_id']) && $ad['category_id']) {
            $category = Categories::getById($ad['category_id']);
            while ($category) {
                $categoryIds[$category->getLevel()] = $category->getId();
                $category = $category->getParentId() ? Categories::getById($category->getParentId()) : null;
            }
        }

        $regionIds = [];
        if (isset($ad['region_id']) && $ad['region_id']) {
            $region = Regions::getById($ad['region_id']);
            while ($region) {
                $regionIds[$region->getLevel()] = $region->getId();
                $region = $region->getParentId() ? Regions::getById($region->getParentId()) : null;
            }
        } else {
            $regionIds = [null];
        }

        AdsLinks::add($ad['id'], $categoryIds, $regionIds);
    }

    private static function getByDonorUrl(string $donorUrl): ?Ad
    {
        $row = Model\Ads::getByDonorUrl($donorUrl);

        return $row
            ? new Ad($row, AdsImages::getAdsImages([$row['id']]), AdsDetails::getAdsDetails([$row['id']]))
            : null;
    }
}