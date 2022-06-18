<?php

namespace Palto\Model;

class Settings extends Model
{
    protected string $name = 'settings';


    public function getAll(): array
    {
        return self::getDb()->query("SELECT * FROM settings ORDER BY `group`, `template`") ?: [];
    }

    public function getByName(string $name)
    {
        return self::getDb()->queryFirstRow("SELECT * FROM settings WHERE name=%s", $name) ?: [];
    }
}