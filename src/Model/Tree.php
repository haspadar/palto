<?php

namespace Palto\Model;

abstract class Tree extends Model
{
    public function getMaxTreeId(): int
    {
        return self::getDb()->queryFirstField('SELECT MAX(tree_id) FROM ' . $this->name) ?: 0;
    }

    public function getByIds(array $ids): array
    {
        return $ids
            ? self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE id IN %ld', $ids)
            : [];
    }

    public function getChildrenCount(array $ids): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name . ' WHERE parent_id IN %ld', $ids) ?: 0;
    }

    public function getChildren(array $ids, int $limit = 0, int $offset = 0, string $orderBy = 'id'): array
    {
        return $limit
            ? self::getDb()->query(
                'SELECT * FROM ' . $this->name . ' WHERE parent_id IN %ld ORDER BY ' . $orderBy . ' LIMIT %d OFFSET %d',
                $ids,
                $limit,
                $offset
            ) : self::getDb()->query(
                'SELECT * FROM ' . $this->name . ' WHERE parent_id IN %ld ORDER BY ' . $orderBy,
                $ids,
            );
    }

    public function getChildrenIds(array $categoriesIds, int $level): array
    {
        return array_column(self::getChildren($categoriesIds, $level), 'id');
    }

    public function getByTitle(string $title, int $parentId = 0): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT * FROM ' . $this->name . ' WHERE url = %s AND parent_id ' . ($parentId ? '=' . $parentId : 'IS NULL'),
            $title
        ) ?: [];
    }

    public function getByUrl(string $url): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE url = %s', $url) ?: [];
    }

    public function getLeafs(int $limit): array
    {
        $query = "SELECT * FROM " . $this->name . " WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)";
        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return self::getDb()->query($query);
    }

    public function getByDonorUrl(string $donorUrl, int $level): array
    {
        if ($donorUrl) {
            return self::getDb()->queryFirstRow(
                'SELECT * FROM ' . $this->name . ' WHERE donor_url = %s AND level = %d',
                $donorUrl,
                $level
            ) ?: [];
        }

        return [];
    }

    public function getMaxLevel(): int
    {
        return self::getDb()->queryFirstField('SELECT MAX(level) FROM ' . $this->name) ?: 0;
    }

    public function findByUrlAll(string $url, string $orderBy = 'level'): array
    {
        return self::getDb()->query("SELECT * FROM " . $this->name . " WHERE url LIKE %s ORDER BY $orderBy", $url . '%') ?: [];
    }

    public function getRoots(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE level = %d ORDER BY title', 1) ?: [];
    }

    public function removeChildren(int $parentId)
    {
        self::getDb()->delete($this->name, 'parent_id = %d', $parentId);
    }

    public function getParents(int $id): array
    {
        $category = $this->getById($id);
        $parents = [];
        while ($category['parent_id']) {
            $category = $this->getById($category['parent_id']);
            $parents[] = $category;
        }

        return $parents;
    }
}