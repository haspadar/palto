<?php

namespace Palto;

class CategoriesRegionsWithAds
{
    public static function add(int $categoryId, ?int $regionId)
    {
        $childCategory = new Category(\Palto\Model\Categories::getById($categoryId));
        $childRegion = $regionId ? new Region(\Palto\Model\Regions::getById($regionId)) : null;
        foreach (array_merge($childCategory->getParents(), [$childCategory]) as $category) {
            if ($childRegion) {
                foreach (array_merge($childRegion->getParents(), [$childRegion]) as $region) {
                    \Palto\Model\CategoriesRegionsWithAds::add(
                        $category->getId(),
                        $region->getId()
                    );
                }
            } else {
                \Palto\Model\CategoriesRegionsWithAds::add($category->getId(), null);
            }

        }
    }
}