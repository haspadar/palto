<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Logger;
use Palto\Region;

class Categories extends NestedSet
{
    protected string $name = 'categories';

    public function rebuildAdsCount()
    {
        $limit = 1000;
        $offset = 0;
        self::getDb()->query("UPDATE categories SET ads_count = 0");
        while ($categories = self::getDb()->query("SELECT id,title,parent_id FROM " . $this->name . " WHERE id IN(SELECT DISTINCT category_id FROM ads) LIMIT %d OFFSET %d", $limit, $offset)) {
            foreach ($categories as $category) {
                $adsCount = self::getDb()->queryFirstField("SELECT COUNT(*) FROM ads WHERE category_id = %d", $category['id']);
                if ($adsCount > 0) {
                    Logger::info('Updating ads count for category "' . $category['title'] . " and parents");
                    $this->updateAdsCounts($adsCount, $category);
                }
            }

            $offset += $limit;
        }
    }

    public function findByTitle(string $title, int $parentId): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE LOWER(title) = LOWER(%s)' . ($parentId ? ' AND parent_id = ' . $parentId : ''), $title) ?: [];
    }

    public function getLiveCategoriesWithChildren(int $limit = 0, int $childrenMinimumCount = 5): array
    {
        $query = 'SELECT c.*, COUNT(c2.id) AS count FROM categories AS c INNER JOIN ' . $this->name . ' AS c2 ON c.id = c2.parent_id';
        $query .= " WHERE (c.id IN (SELECT DISTINCT category_level_1_id FROM ads) OR c.id IN (SELECT DISTINCT category_level_2_id FROM ads))";
        $query .= " AND c.parent_id IS NULL";
        $query .= ' GROUP BY c.id HAVING count >= %d_count';
        $values = ['count' => $childrenMinimumCount];
        if ($limit) {
            $query .= ' LIMIT %d_limit';
            $values['limit'] = $limit;
        }

        return self::getDb()->query($query, $values);
    }

    public function getLiveCategories(?Category $category, ?Region $region, int $limit = 0): array
    {
        $query = 'SELECT * FROM ' . $this->name . ' AS c';
        $values = [];
        if ($region && $region->getId()) {
            $query .= " INNER JOIN live AS crwa ON c.id = crwa.category_id WHERE crwa.region_id = " . $region->getId();
        } else {
            $query .= " WHERE (c.id IN (SELECT DISTINCT category_level_1_id FROM ads) OR c.id IN (SELECT DISTINCT category_level_2_id FROM ads))";
        }

        if ($category) {
            $query .= " AND c.parent_id = %d_parent_id";
            $values['parent_id'] = $category->getId();
        } else {
            $query .= " AND c.parent_id IS NULL";
        }

        $query .= " ORDER BY ads_count DESC";
        if ($limit) {
            $query .= ' LIMIT %d_limit';
            $values['limit'] = $limit;
        }

        return self::getDb()->query($query, $values);
    }
}