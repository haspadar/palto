<?php

namespace Palto\Model;

use Palto\Category;

class Categories extends Model
{
    public static function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $id) ?: [];
    }

    public static function getCategoriesByIds(array $categoryIds): array
    {
        return $categoryIds
            ? self::getDb()->query('SELECT * FROM categories WHERE id IN %ld', $categoryIds)
            : [];
    }

    public static function getChildLevelCategoriesIds(array $categoriesIds, int $level): array
    {
        return self::getDb()->queryFirstColumn(
            'SELECT id FROM categories WHERE parent_id IN %ld AND level = %d',
            $categoriesIds,
            $level
        );
    }

    public static function getWithAdsCategories(?Category $parentCategory, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        if (!$parentCategory || $parentCategory->getLevel() < 3) {
            $query = 'SELECT * FROM categories';
            $values = [];
            $adsFieldCategory = 'category_level_' . ($parentCategory ? $parentCategory->getLevel() + 1 : 1) . '_id';
            $query .= " WHERE id IN (SELECT $adsFieldCategory FROM ads)";
            if ($parentCategory) {
                $query .= ' AND parent_id = %d_parent_id';
                $values['parent_id'] = $parentCategory->getId();
            }

            if ($orderBy) {
                $query .= ' ORDER BY ' .$orderBy;
            }

            if ($limit) {
                $query .= ' LIMIT %d_limit OFFSET %d_offset';
                $values['limit'] = $limit;
                $values['offset'] = $offset;
            }

            return self::getDb()->query($query, $values);
        } else {
            return [];
        }
    }
    
    public static function getByUrl(string $url, int $level, int $excludeId = 0): array
    {
        if ($url) {
            return self::getDb()->queryFirstRow(
                'SELECT * FROM categories WHERE url = %s AND level = %d AND id <> %d',
                $url,
                $level,
                $excludeId
            ) ?: [];
        }

        return [];
    }

    public static function getLeafs(): array
    {
        return self::getDb()->query(
            "SELECT * FROM categories WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)"
        );
    }

    public static function add(array $category): int
    {
        self::getDb()->insert('categories', $category);

        return self::getDb()->insertId();
    }

    public static function getByDonorUrl(string $donorUrl, int $level): array
    {
        if ($donorUrl) {
            return self::getDb()->queryFirstRow(
                'SELECT * FROM categories WHERE donor_url = %s AND level = %d',
                $donorUrl,
                $level
            ) ?: [];
        }

        return [];
    }
}