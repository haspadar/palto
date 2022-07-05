<?php

namespace Palto\Model;

use Palto\Category;
use Palto\Debug;
use Palto\Region;
use Palto\Url;

class Pages extends Model
{
    protected string $name = 'pages';

    public function getUniqueUrls(): array
    {
        return self::getDb()->query(
            'SELECT * FROM '
            . $this->name
            . ' WHERE router_priority > 0 GROUP BY url ORDER BY router_priority'
        );
    }

    public function getPages(int $templateId = 0, string $orderBy = ''): array
    {
        return self::getDb()->query(
            'SELECT p.*, t.name AS template_name FROM '
                . $this->name
                . ' AS p LEFT JOIN '
                . (new Templates)->getName()
                . ' AS t ON p.template_id=t.id'
                . ($templateId ? ' WHERE p.template_id=' . $templateId : '')
                . ($orderBy ? ' ORDER BY ' . $orderBy : '')
        );
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