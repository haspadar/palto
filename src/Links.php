<?php

namespace Palto;

use Palto\Model\CategoryLinks;
use Palto\Model\RegionLinks;

class Links
{
    public static function updateCategoryLinks()
    {
        CategoryLinks::update();
    }

    public static function updateRegionLinks()
    {
        RegionLinks::update();
    }
}