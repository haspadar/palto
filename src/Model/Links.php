<?php

namespace Palto\Model;

use Palto\Logger;

abstract class Links extends Model
{
    abstract static function update();

    public static function addTableLinks(int $id, $parentId, string $table, string $linksTable, string $linkField)
    {
        $parentIds = self::getParentIds($parentId, $table);
        self::addLinks($id, $parentIds, $linksTable, $linkField);
    }

    public static function updateTableLinks(string $table, string $linksTable, string $linkField)
    {
        self::getDb()->query("TRUNCATE table $linksTable");
        $maxLevel = self::getDb()->query("SELECT MAX(level) as max_level FROM $table")[0]['max_level'];
        for ($level = $maxLevel; $level >= 1; $level--) {
            Logger::debug('Level ' . $level);
            $levelRows = self::getDb()->query("SELECT id, parent_id FROM $table WHERE level = $level");
            Logger::debug('Found ' . count($levelRows) . " $table");
            foreach ($levelRows as $key => $row) {
                $parentIds = self::getParentIds(intval($row['parent_id']), $table);
                self::addLinks(intval($row['id']), $parentIds, $linksTable, $linkField);
                Logger::debug("Added links for $linkField " . ($key + 1) . '/' . count($levelRows) . ' (level ' . $level . ')');
            }
        }
    }

    private static function getParentIds(int $parentId, string $table): array
    {
        $parentIds = [null];
        while ($parent = self::getDb()->query("SELECT * FROM $table WHERE id = $parentId")) {
            $parentIds[] = $parent[0]['id'];
            $parentId = intval($parent[0]['parent_id']);
        }

        return $parentIds;
    }

    private static function addLinks(int $id, array $parentIds, string $linksTable, string $linkField)
    {
        foreach ($parentIds as $parentId) {
            self::getDb()->insert($linksTable, [
                $linkField => $parentId,
                'child_id' => $id
            ]);
        }
    }
}