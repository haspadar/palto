<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;

class Categories
{
    public static function getById(int $id): ?Category
    {
        $category = Model\Categories::getById($id);

        return $category ? new Category($category) : null;
    }

    public static function getByDonorUrl(string $donorCategoryUrl, int $level): ?Category
    {
        $category = Model\Categories::getByDonorUrl($donorCategoryUrl, $level);

        return $category ? new Category($category) : null;
    }

    public static function getByUrl(string $categoryUrl, int $level): ?Category
    {
        $category = Model\Categories::getByUrl($categoryUrl, $level);

        return $category ? new Category($category) : null;
    }

    public static function getWithAdsCategories(?Category $parentCategory = null, $count = 0): array
    {
        $categories = Model\Categories::getWithAdsCategories($parentCategory, $count);

        return array_map(fn($category) => new Category($category), $categories);
    }

    /**
     * @return Category[]
     */
    public static function getLeafs(int $limit = 0): array
    {
        return array_map(
            fn($category) => new Category($category),
            Model\Categories::getLeafs($limit)
        );
    }

    public static function safeAdd(array $category): Category
    {
        $category['create_time'] = (new DateTime())->format('Y-m-d H:i:s');
        if (!isset($category['parent_id']) || !$category['parent_id']) {
            $category['level'] = 1;
            $category['tree_id'] = self::getMaxTreeId() + 1;
        } else {
            $parent = self::getById($category['parent_id']);
            $category['level'] = $parent->getLevel() + 1;
            $category['tree_id'] = $parent->getTreeId();
        }

        $categoryUrl = self::generateUrl($category['title'], $category['level']);
        $found = self::getByUrl($categoryUrl, $category['level']);
        if ($found && $found->getId()) {
            return $found;
        }

        $id = Model\Categories::add($category);

        return new Category(Model\Categories::getById($id));
    }

    public static function generateUrl(string $title, int $level, bool $addSuffix = false): string
    {
        $urlPattern = (new Slugify())->slugify($title);
        $url = $urlPattern;
        $counter = 0;
        if ($addSuffix) {
            while (Model\Categories::getByUrl($url, $level)) {
                $url = $urlPattern . '-' . (++$counter);
            }
        }

        return $url;
    }

    public static function getMaxTreeId(): int
    {
        return \Palto\Model\Regions::getMaxTreeId();
    }

    private static function getChildCategories(array $category): array
    {
        $childrenIds = [];
        $nextLevelCategoriesIds = [$category['id']];
        $level = $category['level'];
        while ($nextLevelCategoriesIds = Model\Categories::getChildLevelCategoriesIds($nextLevelCategoriesIds, ++$level)) {
            $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
        }

        return Model\Categories::getCategoriesByIds($childrenIds);
    }
}