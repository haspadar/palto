<?php

namespace Palto\Model;

class RegionLinks extends Links
{
    public static function update()
    {
        self::updateTableLinks('regions','region_links', 'region_id');
    }
}