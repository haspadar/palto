<?php

namespace Palto;

class Pages
{
    private static Page $currentPage;

    public static function setCurrentPage(Page $page)
    {
        self::$currentPage = $page;
    }

    public static function getCurrentPage(): Page
    {
        return self::$currentPage;
    }

    public static function getPages(int $templateId = 0): array
    {
        return array_map(
            fn(array $page) => new Page($page),
            (new \Palto\Model\Pages())->getPages($templateId)
        );
    }

    public static function getById(int $id)
    {
        return new Page((new \Palto\Model\Pages())->getById($id));
    }

    public static function update(array $setting, int $id)
    {
        (new \Palto\Model\Pages())->update($setting, $id);
    }

    public static function getMainPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('main'));
    }

    public static function getRegistrationsPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('registration'));
    }

    public static function getRegionsPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('regions'));
    }

    public static function getCategoriesPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('categories'));
    }

    public static function getAdPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('ad'));
    }

    public static function getRegionPage(int $level): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('region_' . $level));
    }

    public static function getCategoryPage(int $level): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('category_' . $level));
    }

    public static function get404AdPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('404_ad'));
    }

    public static function get404DefaultPage(): Page
    {
        return new Page((new \Palto\Model\Pages())->getByName('404_default'));
    }
}