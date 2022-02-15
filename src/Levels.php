<?php

namespace Palto;

class Levels
{
    public static function checkCategoriesFields()
    {
        $newFields = self::getNewFields('category_level_%d_id', Categories::getMaxLevel());
        self::addLevelsFields($newFields, 'categories');
    }

    public static function checkRegionsFields()
    {
        $newFields = self::getNewFields('region_level_%d_id', Regions::getMaxLevel());
        self::addLevelsFields($newFields, 'regions');
    }

    public static function updateCategoryLevels()
    {
        self::updateLevels('category_level_%d_id', 'categories', Categories::getMaxLevel());
    }

    public static function updateRegionsLevels()
    {
        self::updateLevels('region_level_%d_id', 'regions', Categories::getMaxLevel());
    }

    private static function getNewFields(string $levelField, int $maxLevel): array
    {
        $existsFields = Ads::getFieldNames();
        $newFields = [];
        for ($level = 1; $level <= $maxLevel; $level++) {
            $field = sprintf($levelField, $level);
            if (!in_array($field, $existsFields)) {
                $newFields[] = $field;
            }
        }

        return $newFields;
    }

    private static function addLevelsFields(array $newFields, string $levelsTable)
    {
        foreach ($newFields as $newField) {
            $query = "ALTER TABLE `ads` ADD `$newField` int(11) unsigned`, ADD FOREIGN KEY (`$newField`) REFERENCES `$levelsTable` (`id`);";
            Model\Ads::getDb()->query($query);
        }
    }

    private static function updateLevels(string $levelField, string $levelsTable, int $maxLevel)
    {
        $field = sprintf($levelField, $maxLevel);
        $baseField = str_replace('level_%d_', '', $levelField);
        Model\Ads::getDb()->query("UPDATE ads SET $field = $baseField WHERE $field IS NULL");
        for ($level = $maxLevel - 1; $level >= 1; $level--) {
            Logger::debug('Update level ' . $level);
            $parentField = sprintf($levelField, $level + 1);
            $field = sprintf($levelField, $level);
            Model\Ads::getDb()->query("UPDATE ads AS a INNER JOIN $levelsTable AS l ON a.$parentField = l.id SET a.$field = l.parent_id WHERE a.$field IS NULL");
        }
    }
}