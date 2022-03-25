<?php
namespace Palto;

class Strategy
{
    public static function isCategory(): bool
    {
        return Regions::getCount() > 1;
    }
}