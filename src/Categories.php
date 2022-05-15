<?php

namespace Palto;

use Cocur\Slugify\Slugify;
use DateTime;

class Categories
{
    public static function findByTitle(string $title, ?Category $parent): ?Category
    {
        $category = (new Model\Categories)->findByTitle($title, $parent ? $parent->getId() : 0);

        return $category ? new Category($category) : null;
    }

    public static function rebuildTree()
    {
        (new \Palto\Model\Categories())->rebuildTree();
    }

    public static function createUndefined(?Category $category = null): Category
    {
        $url = 'undefined' . ($category ? '-' . $category->getUrl() : '');
        $level = $category ? $category->getLevel() + 1 : 1;
        $foundCategory = (new Model\Categories)->getByUrl($url);
        if (!$foundCategory) {
            $id = (new Model\Categories)->add([
                'title' => 'Undefined' . ($category ? ' ' . $category->getTitle() : ''),
                'url' => $url,
                'parent_id' => $category?->getId(),
                'level' => $level
            ]);
            self::rebuildTree();
            $foundCategory = (new Model\Categories)->getById($id);
        }

        return new Category($foundCategory);
    }

    public static function getChildrenCount(array $ids): int
    {
        return $ids ? (new Model\Categories)->getChildrenCount($ids) : 0;
    }

    public static function getChildren(array $ids, int $limit = 0, int $offset = 0, string $orderBy = 'id'): array
    {
        $rows = (new Model\Categories)->getChildren($ids, $limit, $offset, $orderBy);
        $children = [];
        foreach ($rows as $row) {
            $children[(new Category($row))->getParentId()][] = new Category($row);
        }

        return $children;
    }

    public static function getById(int $id): ?Category
    {
        $category = (new Model\Categories)->getById($id);

        return $category ? new Category($category) : null;
    }

    public static function getByDonorUrl(string $donorCategoryUrl, int $level): ?Category
    {
        $category = (new Model\Categories)->getByDonorUrl($donorCategoryUrl, $level);

        return $category ? new Category($category) : null;
    }

    public static function getByTitle(string $categoryTitle, int $parentId = 0): ?Category
    {
        $category = (new Model\Categories)->getByTitle($categoryTitle, $parentId);

        return $category ? new Category($category) : null;
    }

    public static function getByUrl(string $categoryUrl, int $level): ?Category
    {
        $category = (new Model\Categories)->getByUrl($categoryUrl);

        return $category ? new Category($category) : null;
    }

    /**
     * @param Category|null $parentCategory
     * @param Region|null $region
     * @param int $count
     * @return Category[]
     */
    public static function getLiveCategories(?Category $parentCategory = null, ?Region $region = null, int $count = 0): array
    {
        $categories = (new Model\Categories)->getLiveCategories($parentCategory, $region, $count);

        return array_map(fn($category) => new Category($category), $categories);
    }

    public static function getLiveCategoriesWithChildren($count = 0, int $childrenMinimumCount = 0): array
    {
        $rows = $childrenMinimumCount > 0
            ? (new Model\Categories)->getLiveCategoriesWithChildren($count, $childrenMinimumCount)
            : (new Model\Categories)->getLiveCategories(null, null, $count);

        return array_map(fn($category) => new Category($category), $rows);
    }

    /**
     * @return Category[]
     */
    public static function getLeafs(int $limit = 0): array
    {
        return array_map(
            fn($category) => new Category($category),
            (new Model\Categories)->getLeafs($limit)
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

        $id = (new Model\Categories)->add($category);
        self::rebuildTree();

        return new Category((new Model\Categories)->getById($id));
    }

    public static function generateUrl(string $title, int $level, bool $addSuffix = false): string
    {
        $urlPattern = (new Slugify())->slugify($title);
        $url = $urlPattern;
        $counter = 0;
        if ($addSuffix) {
            while ((new Model\Categories)->getByUrl($url)) {
                $url = $urlPattern . '-' . (++$counter);
            }
        }

        return $url;
    }

    public static function getMaxTreeId(): int
    {
        return (new Model\Categories)->getMaxTreeId();
    }

    public static function update(array $updates, int $id)
    {
        (new Model\Categories)->update($updates, $id);
    }

    public static function getMaxLevel(): int
    {
        return (new Model\Categories)->getMaxLevel();
    }

    /**
     * @return Category[]
     */
    public static function getUndefinedAll(string $orderBy = 'level DESC'): array
    {
        return array_map(
            fn($category) => new Category($category),
            (new Model\Categories)->findByUrlAll('undefined', $orderBy)
        );
    }

    /**
     * @return Category[]
     */
    public static function getRoots(): array
    {
        return array_map(
            fn($category) => new Category($category),
            (new Model\Categories)->getRoots()
        );
    }
}