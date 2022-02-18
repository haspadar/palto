<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class Categories extends Model
{
    public static function getById(int $id): array
    {
        return self::getConnection()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $id) ?: [];
    }

    public static function getCategoriesByIds(array $categoryIds): array
    {
        return $categoryIds
            ? self::getConnection()->query('SELECT * FROM categories WHERE id IN %ld', $categoryIds)
            : [];
    }

    public static function getChildren(array $categoriesIds, int $level, int $limit = 0): array
    {
        return $limit
            ? self::getConnection()->query(
                'SELECT * FROM categories WHERE parent_id IN %ld AND level = %d LIMIT %d',
                $categoriesIds,
                $level,
                $limit
            ) : self::getConnection()->queryFirstColumn(
                'SELECT * FROM categories WHERE parent_id IN %ld AND level = %d',
                $categoriesIds,
                $level
            );
    }

    public static function getChildrenIds(array $categoriesIds, int $level): array
    {
        return array_column(self::getChildren($categoriesIds, $level), 'id');
    }

    public static function getLiveCategoriesWithChildren(int $limit = 0, int $childrenMinimumCount = 5): array
    {
        $query = 'SELECT c.*, COUNT(c2.id) AS count FROM categories AS c INNER JOIN categories AS c2 ON c.id = c2.parent_id';
        $query .= " WHERE c.id IN (SELECT DISTINCT category_id FROM categories_regions_with_ads)";
        $query .= " AND c.parent_id IS NULL";
        $query .= ' GROUP BY c.id HAVING count >= %d_count';
        $values = ['count' => $childrenMinimumCount];
        if ($limit) {
            $query .= ' LIMIT %d_limit';
            $values['limit'] = $limit;
        }

        return self::getConnection()->query($query, $values);
    }

    public static function getLiveCategories(?Category $category, ?Region $region, int $limit = 0): array
    {
        $query = 'SELECT * FROM categories AS c';
        $values = [];
        if ($region && $region->getId()) {
            $query .= " INNER JOIN categories_regions_with_ads AS crwa ON c.id = crwa.category_id WHERE crwa.region_id = " . $region->getId();
        } else {
            $query .= " WHERE c.id IN (SELECT DISTINCT category_id FROM categories_regions_with_ads)";
        }

        if ($category) {
            $query .= " AND c.parent_id = %d_parent_id";
            $values['parent_id'] = $category->getId();
        } else {
            $query .= " AND c.parent_id IS NULL";
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit';
            $values['limit'] = $limit;
        }

        return self::getConnection()->query($query, $values);
    }
    
    public static function getByUrl(string $url, int $level, int $excludeId = 0): array
    {
        if ($url) {
            return self::getConnection()->queryFirstRow(
                'SELECT * FROM categories WHERE url = %s AND level = %d AND id <> %d',
                $url,
                $level,
                $excludeId
            ) ?: [];
        }

        return [];
    }

    public static function getLeafs(int $limit): array
    {
        $query = "SELECT * FROM categories WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)";
        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return self::getConnection()->query($query);
    }

    public static function add(array $category): int
    {
        self::getConnection()->insert('categories', $category);

        return self::getConnection()->insertId();
    }

    public static function getByDonorUrl(string $donorUrl, int $level): array
    {
        if ($donorUrl) {
            return self::getConnection()->queryFirstRow(
                'SELECT * FROM categories WHERE donor_url = %s AND level = %d',
                $donorUrl,
                $level
            ) ?: [];
        }

        return [];
    }

    public static function update(array $updates, int $id)
    {
        self::getConnection()->update('categories', $updates, 'id = %d', $id);
    }

    public static function getMaxLevel(): int
    {
        return self::getConnection()->createQueryBuilder()
            ->select('MAX(level)')
            ->from('categories')
            ->fetchOne() ?: 0;
    }

}