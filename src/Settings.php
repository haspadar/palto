<?php

namespace Palto;

class Settings
{
    public static function getValues(): array
    {
        return (new \Palto\Model\Settings())->getAll();
    }

    public static function getByName(string $name)
    {
        return (new \Palto\Model\Settings())->getByName($name);
    }

    public static function getById(int $id)
    {
        return (new \Palto\Model\Settings())->getById($id);
    }

    public static function update(array $setting, int $id)
    {
        (new \Palto\Model\Settings())->update($setting, $id);
    }
}