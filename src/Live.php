<?php

namespace Palto;

class Live
{
    public static function addIgnore(?int $categoryId, ?int $regionId): int
    {
        return (new \Palto\Model\Live)->addIgnore([
            'category_id' => $categoryId ?: null,
            'region_id' => $regionId ?: null
        ]);
    }

    public static function rebuild()
    {
        $adsModel = new \Palto\Model\Ads();
        $pairsCount = $adsModel->getPairsCount();
        $limit = 1000;
        $offset = 0;
        $createTime = (new \DateTime())->format('Y-m-d H:i:s');
        while ($pairs = $adsModel->getPairs($limit, $offset)) {
            Logger::info('Adding ' . ($offset + count($pairs) . '/' . $pairsCount . ' pairs'));
            foreach ($pairs as $pair) {
                foreach (self::getPairCombinations($pair) as $combination) {
                    (new \Palto\Model\Live())->addUpdate([
                        'category_id' => $combination['category_id'],
                        'region_id' => $combination['region_id'],
                        'create_time' => $createTime
                    ], ['create_time' => $createTime]);
                }
            }

            $offset += 1000;
        }

        $removedCount = (new \Palto\Model\Live())->removeEarly($createTime);
        Logger::info('Removed ' . $removedCount . ' old pairs');
    }

    private static function getPairCombinations(array $pair): array
    {
        $categoryIds = array_merge([$pair['category_id']], array_map(
            fn(Category $category) => $category->getId(),
            Categories::getParents($pair['category_id'])
        ), [null]);
        $regionIds = array_merge([$pair['region_id']], array_map(
            fn(Region $region) => $region->getId(),
            Regions::getParents($pair['region_id'])
        ), [null]);
        $pairs = [];
        foreach ($categoryIds as $categoryId) {
            foreach ($regionIds as $regionId) {
                $pairs[] = [
                    'category_id' => $categoryId,
                    'region_id' => $regionId
                ];
            }
        }

        return $pairs;
    }
}