<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Translates extends Model
{
    protected string $name = 'translates';

    public function getTranslates(): array
    {
        return self::getDb()->query('SELECT * FROM translates');
    }
}