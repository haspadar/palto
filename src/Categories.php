<?php

namespace Palto;

class Categories
{
    public static function getById(int $id): ?Category
    {
        $category = \Palto\Model\Categories::getById($id);

        return $category ? new Category($category) : null;
    }

    public static function getByUrl(string $categoryUrl, int $level): ?Category
    {
        $category = \Palto\Model\Categories::getByUrl($categoryUrl, $level);

        return $category ? new Category($category) : null;
    }

    public static function getWithAdsCategories(?Category $parentCategory = null, $count = 0): array
    {
        $categories = \Palto\Model\Categories::getWithAdsCategories($parentCategory, $count);

        return array_map(fn($category) => new Category($category), $categories);
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