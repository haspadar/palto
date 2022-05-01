<?php

namespace Palto\Model;

class Synonyms extends Model
{
    public static function has(string $title, int $categoryId): bool
    {
        return (bool)self::getDb()->queryFirstRow('SELECT * FROM synonyms WHERE title = %s AND category_id = %d', $title, $categoryId);
    }

    public static function add(string $title, int $categoryId): int
    {
        self::getDb()->insert('synonyms', [
            'title' => $title,
            'category_id' => $categoryId
        ]);

        return self::getDb()->insertId();
    }

    public static function getAll()
    {
        return self::getDb()->query('SELECT * FROM synonyms ORDER BY title DESC');
    }
}