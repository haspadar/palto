<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;
use Monolog\Handler\ZendMonitorHandler;

class Categories
{
    public static function getChildren(array $ids, int $level, int $limit = 0): array
    {
        $rows = Model\Categories::getChildren($ids, $level, $limit);
        $children = [];
        foreach ($rows as $row) {
            $category = new Category($row);
            $children[$category->getParentId()][] = $category;
        }

        return $children;
    }

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

    /**
     * @param Category|null $parentCategory
     * @param Region|null $region
     * @param $count
     * @return Category[]
     */
    public static function getLiveCategories(?Category $parentCategory = null, ?Region $region = null, int $count = 0): array
    {
        $categories = Model\Categories::getLiveCategories($parentCategory, $region, $count);

        return array_map(fn($category) => new Category($category), $categories);
    }

    public static function getLiveCategoriesWithChildren($count = 0, int $childrenMinimumCount = 0): array
    {
        $rows = $childrenMinimumCount > 0
            ? Model\Categories::getLiveCategoriesWithChildren($count, $childrenMinimumCount)
            : Model\Categories::getLiveCategories(null, null, $count);

        return array_map(fn($category) => new Category($category), $rows);
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

        $category['url'] = self::generateUrl($category['title'], $category['level']);
        $found = self::getByUrl($category['url'], $category['level']);
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
        return \Palto\Model\Categories::getMaxTreeId();
    }

    public static function update(array $updates, int $id)
    {
        \Palto\Model\Categories::update($updates, $id);
    }

    public static function getMaxLevel(): int
    {
        return \Palto\Model\Categories::getMaxLevel();
    }

    private static function getChildCategories(array $category): array
    {
        $childrenIds = [];
        $nextLevelCategoriesIds = [$category['id']];
        $level = $category['level'];
        while ($nextLevelCategoriesIds = Model\Categories::getChildrenIds($nextLevelCategoriesIds, ++$level)) {
            $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
        }

        return Model\Categories::getCategoriesByIds($childrenIds);
    }
}