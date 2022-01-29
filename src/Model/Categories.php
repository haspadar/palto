<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

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

    public static function getWithAdsCategories(?Category $category, ?Region $region, int $limit = 0, $offset = 0, $orderBy = ''): array
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

        if ($orderBy) {
            $query .= ' ORDER BY ' .$orderBy;
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit OFFSET %d_offset';
            $values['limit'] = $limit;
            $values['offset'] = $offset;
        }

        return self::getDb()->query($query, $values);
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

    public static function getLeafs(int $limit): array
    {
        $query = "SELECT * FROM categories WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)";
        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return self::getDb()->query($query);
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

    public static function update(array $updates, int $id)
    {
        self::getDb()->update('categories', $updates, 'id = %d', $id);
    }
}