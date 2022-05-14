<?php

namespace Palto\Model;

use ICanBoogie\Inflector;
use Palto\Debug;
use Palto\Synonym;

class Synonyms extends Model
{
    public static function has(Synonym $synonym, int $categoryId): bool
    {
        return (bool)self::getDb()->queryFirstRow('SELECT * FROM synonyms WHERE title = %s AND category_id = %d', $synonym->getTitle(), $categoryId);
    }

    public static function remove(string $title, int $categoryId): void
    {
        self::getDb()->delete('synonyms', 'title = %s AND category_id = %d', $title, $categoryId);
    }

    public static function add(string $title, int $categoryId): int
    {
        self::getDb()->insertIgnore('synonyms', [
            'title' => $title,
            'category_id' => $categoryId
        ]);

        return self::getDb()->insertId();
    }

    public static function getAll(int $categoryId = 0): array
    {
        return self::getDb()->query(
            "SELECT s.*, s.category_id, SUM(LENGTH(s.title) - LENGTH(REPLACE(s.title, ' ', '')) + 1) as spaces_count FROM synonyms AS s "
                . " INNER JOIN categories AS c ON s.category_id = c.id "
                . ($categoryId ? 'WHERE category_id=' . $categoryId : '')
                . " GROUP BY c.title ORDER BY IF(c.url like 'undefined%', 1, 0), spaces_count DESC"
        );
    }

    public static function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM synonyms WHERE id = %d', $id) ?: [];
    }

    public static function getByTitle(string $title): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM synonyms WHERE title = %s', $title) ?: [];
    }

    public static function getTitles(int $categoryId): array
    {
        return array_column(
            self::getDb()->query('SELECT title FROM synonyms WHERE category_id = %d ORDER BY title DESC', $categoryId) ?: [],
            'title'
        );
    }
}