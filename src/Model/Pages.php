<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Pages extends Model
{
    protected string $name = 'pages';

    public function getPages(int $templateId = 0): array
    {
        return self::getDb()->query('SELECT p.*, t.name AS template_name FROM ' . $this->name . ' AS p LEFT JOIN ' . (new Templates)->getName() . ' AS t ON p.template_id=t.id');
    }

    public function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT p.*, t.name AS template_name FROM ' . $this->name . ' AS p LEFT JOIN ' . (new Templates)->getName() . ' AS t ON p.template_id=t.id WHERE p.id = %d', $id) ?: [];
    }

    public function getByName(string $name): array
    {
        return self::getDb()->queryFirstRow('SELECT p.*, t.name AS template_name FROM ' . $this->name . ' AS p LEFT JOIN ' . (new Templates)->getName() . ' AS t ON p.template_id=t.id WHERE p.name = %s', $name) ?: [];
    }
}