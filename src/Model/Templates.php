<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Templates extends Model
{
    protected string $name = 'templates';

    public function getTemplates(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name);
    }
}