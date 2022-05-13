<?php

namespace Palto;

use DateTime;
use Exception;
use Palto\Model\AdsDetails;
use Palto\Model\AdsImages;
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

    public static function getCategoriesAdsCounts(array $categoriesIds, int $level = 0): array
    {
        return $categoriesIds
            ? Model\Ads::getCategoriesAdsCounts($categoriesIds, $level)
            : [];
    }

    public static function getAdsCount(array $categoriesIds = []): int
    {
        return Model\Ads::getAdsCount($categoriesIds);
    }

    public static function getHotAds(?Region $region, int $limit): array
    {
        return self::getAds(
            $region,
            Categories::getById(Config::get('HOT_LAYOUT_HOT_CATEGORY')),
            $limit
        );
    }

    public static function getFields(array $categories, array $fields, int $limit, int $offset): array
    {
        $categoriesIds = array_map(fn(Category $category) => $category->getId(), $categories);

        return \Palto\Model\Ads::getFields($categoriesIds, $fields, $limit, $offset);
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
            $ad = self::addLevels($ad);
            try {
                $adId = Model\Ads::add($ad);
                CategoriesRegionsWithAds::add($ad['category_id'], $ad['region_id']);
            } catch (Exception $e) {
                Logger::error(var_export($ad, true));
                Logger::error($e->getMessage());
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
                    } catch (Exception $e) {
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

    private static function addLevels(array &$ad): array
    {
        if (isset($ad['category_id']) && $ad['category_id']) {
            $category = Categories::getById($ad['category_id']);
            while ($category && $category->getLevel()) {
                $ad['category_level_' . $category->getLevel() . '_id'] = $category->getId();
                $category = $category->getParentId()
                    ? Categories::getById($category->getParentId())
                    : null;
            }
        }

        if (isset($ad['region_id']) && $ad['region_id']) {
            $region = Regions::getById($ad['region_id']);
            while ($region) {
                $ad['region_level_' . $region->getLevel() . '_id'] = $region->getId();
                $region = $region->getParentId() ? Regions::getById($region->getParentId()) : null;
            }
        }

        return $ad;
    }

    private static function getByDonorUrl(string $donorUrl): ?Ad
    {
        $row = Model\Ads::getByDonorUrl($donorUrl);

        return $row
            ? new Ad($row, AdsImages::getAdsImages([$row['id']]), AdsDetails::getAdsDetails([$row['id']]))
            : null;
    }

    public static function getFieldNames(): array
    {
        return Model\Ads::getFieldNames('ads');
    }

    public static function moveAd(
        int $adId,
        int $categoryLevel1Id,
        string $newCategoryLevel1Title,
        int $categoryLevel2Id,
        string $newCategoryLevel2Title
    ): void {
        if ($newCategoryLevel1Title) {
            $category = Categories::safeAdd(['title' => $newCategoryLevel1Title]);
            self::update([
                'category_id' => $category->getId(),
                'category_level_1_id' => $category->getId(),
                'category_level_2_id' => null,
            ], $adId);
        } elseif ($newCategoryLevel2Title) {
            $category = Categories::safeAdd([
                'title' => $newCategoryLevel2Title,
                'parent_id' => $categoryLevel1Id
            ]);
            self::update([
                'category_id' => $category->getId(),
                'category_level_1_id' => $categoryLevel1Id,
                'category_level_2_id' => $category->getId(),
            ], $adId);
        } elseif ($categoryLevel2Id) {
            self::update([
                'category_id' => $categoryLevel2Id,
                'category_level_1_id' => $categoryLevel1Id,
                'category_level_2_id' => $categoryLevel2Id,
            ], $adId);
        } elseif ($categoryLevel1Id) {
            self::update([
                'category_id' => $categoryLevel1Id,
                'category_level_1_id' => $categoryLevel1Id,
                'category_level_2_id' => null,
            ], $adId);
        }
    }

    public static function update(array $updates, int $id): void
    {
        \Palto\Model\Ads::update($updates, $id);
    }
}