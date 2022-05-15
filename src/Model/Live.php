<?php

namespace Palto\Model;

use Palto\Debug;

class Live extends Model
{
    protected string $name = 'live';

//    public function add(array $data): int
//    {
//        $category = (new Categories)->getById($data['category_id']);
//        if ($data['region_id'] ?? null) {
//            $region = Regions::getById($data['region_id']);
//            $found = self::getDb()->query(
//                'SELECT id FROM ads WHERE category_level_' . $category['level'] . '_id = %d AND region_level_' . $region['level'] . '_id = %d',
//                $data['category_id'],
//                $data['region_id']
//            )[0]['count'];
//        } elseif ($data['category_id']) {
//            $count = self::getDb()->query('SELECT COUNT(*) AS count FROM ads WHERE category_level_' . $category['level'] . '_id = %d', $data['category_id'])[0]['count'];
//        }
//
//        if ($count ?? 0) {
//            self::getDb()->insertUpdate('categories_regions_with_ads', [
//                'category_id' => $data['category_id'],
//                'region_id' => $data['region_id'],
//            ]);
//        }
//    }
    public function removeEarly(string $createTime): int
    {
        self::getDb()->delete($this->name, 'create_time < %s', $createTime);

        return self::getDb()->queryFirstField('SELECT ROW_COUNT()');
    }
}