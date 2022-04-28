<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Region;

class CategoriesCandidates extends Model
{
    public static function getLeafs(int $limit): array
    {
        $query = "SELECT * FROM categories WHERE id NOT IN (SELECT parent_id FROM categories WHERE parent_id IS NOT NULL)";
        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return self::getDb()->query($query);
    }
}