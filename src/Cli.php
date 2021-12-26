<?php

namespace Palto;

class Cli
{
    public static function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }
}