<?php

namespace Palto\Model;

use Palto\Debug;

class Categories extends Model
{
    public static function getCategory(int $categoryId): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $categoryId);
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

    public static function getCategories(int $parentId, int $level = 0, int $limit = 0, $offset = 0, $orderBy = ''): array
    {
        $query = 'SELECT * FROM categories';
        $values = [];
        if ($parentId || $level) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d_parent_id';
            $values['parent_id'] = $parentId;
        }

        if ($level) {
            $query .= ($parentId ? ' AND ' : '') . 'level = %d_level';
            $values['level'] = $level;
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

    public static function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $id);
    }
}