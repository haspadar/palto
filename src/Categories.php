<?php

namespace Palto;

class Categories
{
    public static function getByUrl(string $categoryUrl, int $level): ?Category
    {
        $category = \Palto\Model\Categories::getByUrl($categoryUrl, $level);

        return $category ? new Category($category) : null;
    }

    public static function getWithAdsCategories(int $parentCategoryId = 0, $level = 1): array
    {
        $unfiltered = \Palto\Model\Categories::getCategories($parentCategoryId, $level, 0);
        $categories = array_filter($unfiltered, function (array $category) {
            $childrenIds = array_merge([$category['id']], array_column(self::getChildCategories($category), 'id'));

            return \Palto\Ads::getCategoriesAdsCount($childrenIds) > 0;
        });

        return array_map(fn ($category) => new Category($category), $categories);
    }

    private static function getChildCategories(array $category): array
    {
        $childrenIds = [];
        $nextLevelCategoriesIds = [$category['id']];
        $level = $category['level'];
        while ($nextLevelCategoriesIds = \Palto\Model\Categories::getChildLevelCategoriesIds($nextLevelCategoriesIds, ++$level)) {
            $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
        }

        return \Palto\Model\Categories::getCategoriesByIds($childrenIds);
    }
}