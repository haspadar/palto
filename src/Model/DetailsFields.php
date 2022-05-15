<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;

class DetailsFields extends Model
{
    protected string $name = 'details_fields';

    public function getDetailsFieldId(int $categoryId, string $field): int
    {
        $fieldId = self::getDb()->queryFirstField(
            'SELECT id FROM ' . $this->name . ' WHERE category_id = %d AND field = %s LIMIT 1',
            $categoryId,
            $field
        );
        if (!$fieldId) {
            self::getDb()->insert($this->name, [
                'category_id' => $categoryId,
                'field' => $field
            ]);
            $fieldId = self::getDb()->insertId();
        }

        return $fieldId;
    }
}