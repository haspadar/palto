<?php

namespace Palto\Model;

class RegionLinks extends Links
{
    public static function add(int $id, $parentId)
    {
        self::addTableLinks($id, $parentId, 'regions','region_links', 'region_id');
    }

    public static function update()
    {
        self::updateTableLinks('regions','region_links', 'region_id');
    }
}