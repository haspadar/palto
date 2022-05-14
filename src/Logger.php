<?php

namespace Palto;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;

class Logger
{
    private static \Monolog\Logger $logger;

    public static function log($level, $message, array $context = [])
    {
        self::getLogger()->log($level, $message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::getLogger()->error($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        self::getLogger()->critical($message, $context);
    }

    public static function debug($message, array $context = [])
    {
        self::getLogger()->debug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::getLogger()->info($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::getLogger()->warning($message, $context);
    }

    private static function getLogger(): \Monolog\Logger
    {
        if (!isset(self::$logger)) {
            self::$logger = new \Monolog\Logger('palto');
            $handler = new StreamHandler('php://stdout');
            $handler->setFormatter(new ColoredLineFormatter());
            self::$logger->pushHandler($handler);
            $directory = self::getDirectory();
            self::$logger->pushHandler(new RotatingFileHandler(
                $directory . '/info',
                20,
                \Monolog\Logger::INFO
            ));
            self::$logger->pushHandler(new RotatingFileHandler(
                $directory . '/error',
                20,
                \Monolog\Logger::ERROR
            ));
        }

        return self::$logger;
    }

    private static function getDirectory(): string
    {
        $scriptParts = array_values(array_filter(explode('/', $_SERVER['SCRIPT_FILENAME'])));
        if ($scriptParts[count($scriptParts) - 1] == 'index.php') {
            $isKarman = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])))[0] == 'karman';
            if ($isKarman) {
                return Directory::getRootDirectory() . '/logs/karman';
            } else {
                return Directory::getRootDirectory() . '/logs/site';
            }

        } else {
            $name = str_replace('.php', '', $scriptParts[count($scriptParts) - 1]);

            return Directory::getRootDirectory() . '/logs/' . $name ;
        }
    }
}