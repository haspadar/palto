<?php

namespace Palto\Model;

use Palto\Cli;
use Palto\Config;
use Palto\Debug;
use Palto\Logger;

abstract class Model
{
    private static \MeekroDB $db;

    protected string $name;

    public static function getDb(): \MeekroDB
    {
        if (!isset(self::$db)) {
            self::$db = new \MeekroDB(
                Config::get('DB_HOST') ?? '127.0.0.1',
                Config::get('DB_USER'),
                Config::get('DB_PASSWORD'),
                Config::get('DB_NAME'),
                Config::get('DB_PORT') ?? 3306,
                'utf8mb4'
            );
            if (Config::isDebug() && !Cli::isCli()) {
                self::$db->debugMode();
            }

            $errorHandler = function ($params) {
                Logger::error('Database error: ' . $params['error']);
                Logger::error('Database query: ' . $params['query'] ?? '');

                throw new \Exception('Database error: ' . $params['error']);
            };
            self::$db->error_handler = $errorHandler; // runs on mysql query errors
            self::$db->nonsql_error_handler = $errorHandler; // runs on library errors (bad syntax, etc)
        }

        return self::$db;
    }

    public function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE id = %d', $id) ?: [];
    }

    public function addUpdate(array $data, array $onDuplicateUpdates): int
    {
        self::getDb()->insertUpdate($this->name, $data, $onDuplicateUpdates);

        return self::getDb()->insertId();
    }

    public function addIgnore(array $data): int
    {
        self::getDb()->insertIgnore($this->name, $data);

        return self::getDb()->insertId();
    }

    public function add(array $data): int
    {
        self::getDb()->insert($this->name, $data);

        return self::getDb()->insertId();
    }

    public function update(array $updates, int $id)
    {
        self::getDb()->update($this->name, $updates, 'id = %d', $id);
    }

    public function remove(int $id)
    {
        self::getDb()->delete($this->name, 'id = %d', $id);
    }

    public function getFieldNames(string $name): array
    {
        return array_column(self::getDb()->query("SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE 
                TABLE_SCHEMA = Database()
                AND TABLE_NAME = '$name'"
        ), 'COLUMN_NAME');
    }

    protected function groupByField(array $unGrouped, string $field): array
    {
        $grouped = [];
        foreach ($unGrouped as $data) {
            $grouped[$data[$field]][] = $data;
        }

        return $grouped;
    }
}