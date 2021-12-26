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
            self::$logger->pushHandler(new RotatingFileHandler(
                Directory::getRootDirectory() . '/logs/parser',
                20,
                \Monolog\Logger::INFO
            ));
        }

        return self::$logger;
    }
}