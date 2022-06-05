<?php

namespace Palto\Model;

use Palto\Debug;
use Palto\Synonym;

class Synonyms extends Model
{
    protected string $name = 'synonyms';

    public function has(Synonym $synonym, int $categoryId): bool
    {
        return (bool)self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE title = %s AND category_id = %d', $synonym->getTitle(), $categoryId);
    }

    public function removeBy(string $title, int $categoryId): void
    {
        self::getDb()->delete('synonyms', 'title = %s AND category_id = %d', $title, $categoryId);
    }

    public function getAll(int $categoryId = 0): array
    {
        return self::getDb()->query(
            "SELECT s.*, s.category_id, SUM(LENGTH(s.title) - LENGTH(REPLACE(s.title, ' ', ''))) as spaces_count FROM " . $this->name . " AS s "
                . " INNER JOIN categories AS c ON s.category_id = c.id "
                . ($categoryId ? 'WHERE category_id=' . $categoryId : '')
                . " GROUP BY s.id ORDER BY IF(c.url like 'undefined%', 1, 0), spaces_count DESC"
        );
    }

    public function getByTitle(string $title): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE title = %s', $title) ?: [];
    }

    public function getTitles(int $categoryId): array
    {
        return array_column(
            self::getDb()->query('SELECT title FROM ' . $this->name . ' WHERE category_id = %d ORDER BY title DESC', $categoryId) ?: [],
            'title'
        );
    }
}