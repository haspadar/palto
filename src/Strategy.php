<?php
namespace Palto;

class Strategy
{
    public static function isSingleCategory(): bool
    {
        return Categories::getCount() == 1;
    }
}