<?php

namespace Palto;

class Levels
{
    public static function checkCategories()
    {
        $existsFields = Ads::getFieldNames();
        $maxLevel = Categories::getMaxLevel();
        $newFields = [];
        for ($level = 1; $level <= $maxLevel; $level++) {
            $field = "category_level_{$level}_id";
            if (!in_array($field, $existsFields)) {
                $newFields[] = $field;
            }
        }

        foreach ($newFields as $newField) {
            Logger::debug('Alter table ads: adding ' . $newField);
            \Palto\Model\Ads::getDb()->query("ALTER TABLE `ads`
ADD `$newField` int(11) unsigned NULL AFTER `category_id`,
ADD FOREIGN KEY (`$newField`) REFERENCES `categories` (`id`);");
        }

        \Palto\Model\Ads::getDb()->query("UPDATE ads SET category_level_{$maxLevel}_id = category_id WHERE category_level_{$maxLevel}_id IS NULL");
        for ($level = $maxLevel - 1; $level >= 1; $level--) {
            Logger::debug('Update level ' . $level);
            \Palto\Model\Ads::getDb()->query("UPDATE ads AS a INNER JOIN categories AS c ON a.category_level_{($level + 1)}_id = c.id SET category_level_{$level}_id = c.parent_id WHERE a.category_level_{($level + 1)}_id IS NULL");
        }
    }

    public static function addRegionLevel(int $maxLevel)
    {

    }
}