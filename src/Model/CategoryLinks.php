<?php

namespace Palto\Model;

class CategoryLinks extends Links
{
    public static function update()
    {
        self::updateTableLinks('categories','category_links', 'category_id');
    }
}