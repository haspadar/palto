<?php

namespace Palto\Model;

use Palto\Cli;
use Palto\Config;
use Palto\Debug;
use Palto\Logger;

class Model
{
    private static \MeekroDB $db;

    public static function getDb(): \MeekroDB
    {
        if (!isset(self::$db)) {
            self::$db = new \MeekroDB(
                Config::get('DB_HOST') ?? '127.0.0.1',
                Config::get('DB_USER'),
                Config::get('DB_PASSWORD'),
                Config::get('DB_NAME'),
                Config::get('DB_PORT') ?? 3306,
                'utf8'
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

    protected static function groupByField(array $unGrouped, string $field): array
    {
        $grouped = [];
        foreach ($unGrouped as $data) {
            $grouped[$data[$field]][] = $data;
        }

        return $grouped;
    }
}