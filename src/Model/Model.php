<?php

namespace Palto\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Palto\Config;

class Model
{
    private static Connection $connection;

    public static function getConnection(): Connection
    {
        if (!isset(self::$connection)) {
            $connectionParams = [
                'dbname' => Config::get('DB_NAME'),
                'user' => Config::get('DB_USER'),
                'password' => Config::get('DB_PASSWORD'),
                'host' => Config::get('DB_HOST') ?? '127.0.0.1',
                'driver' => 'mysqli',
                'charset' => 'utf8mb4'
            ];
            self::$connection = DriverManager::getConnection($connectionParams);
        }

        return self::$connection;
    }

    public static function getFieldNames(string $name): array
    {
        return self::getConnection()->createQueryBuilder()
            ->select('COLUMN_NAME')
            ->from('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_SCHEMA = Database()')
            ->andWhere('TABLE_NAME = ?')
            ->setParameter(0, $name)
            ->fetchFirstColumn();
    }
}