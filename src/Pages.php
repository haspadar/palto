<?php

namespace Palto;

class Pages
{
    public static function getPages(): array
    {
        return (new \Palto\Model\Pages())->getPages();
    }

    public static function getById(int $id)
    {
        return (new \Palto\Model\Pages())->getById($id);
    }

    public static function update(array $setting, int $id)
    {
        (new \Palto\Model\Pages())->update($setting, $id);
    }
}