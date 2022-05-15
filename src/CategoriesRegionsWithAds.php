<?php

namespace Palto;

class CategoriesRegionsWithAds
{
    public static function add(int $categoryId, ?int $regionId)
    {
        $childCategory = new Category((new \Palto\Model\Categories)->getById($categoryId));
        $childRegion = $regionId ? new Region((new \Palto\Model\Regions)->getById($regionId)) : null;
        foreach (array_merge($childCategory->getParents(), [$childCategory]) as $category) {
            if ($childRegion) {
                foreach (array_merge($childRegion->getParents(), [$childRegion]) as $region) {
                    (new \Palto\Model\CategoriesRegionsWithAds)->add([
                        'category_id' => $category->getId(),
                        'region_id' => $region->getId()
                    ]);
                }
            } else {
                (new \Palto\Model\CategoriesRegionsWithAds)->add($category->getId(), null);
            }
        }
    }
}