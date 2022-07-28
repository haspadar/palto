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

    public static function getUniqueUrls(): array
    {
        return array_map(
            fn(array $page) => new Page($page),
            (new Model\Pages())->getUniqueUrls()
        );
    }

    public static function getPages(int $templateId = 0, string $orderBy = ''): array
    {
        return array_map(
            fn(array $page) => new Page($page),
            (new Model\Pages())->getPages($templateId, $orderBy)
        );
    }

    public static function getById(int $id)
    {
        return new Page((new Model\Pages())->getById($id));
    }

    public static function update(array $setting, int $id)
    {
        (new Model\Pages())->update($setting, $id);
    }

    public static function getMainPage(): Page
    {
        return new Page((new Model\Pages())->getByName('main'));
    }

    public static function getAddPage(): Page
    {
        return new Page((new Model\Pages())->getByName('add'));
    }

    public static function getRegistrationsPage(): Page
    {
        return new Page((new Model\Pages())->getByName('registration'));
    }

    public static function getRegionsPage(): Page
    {
        return new Page((new Model\Pages())->getByName('regions'));
    }

    public static function getCategoriesPage(): Page
    {
        return new Page((new Model\Pages())->getByName('categories'));
    }

    public static function getAdPage(): Page
    {
        return new Page((new Model\Pages())->getByName('ad'));
    }

    public static function getRegionPage(int $level): Page
    {
        return new Page((new Model\Pages())->getByName('region_' . $level));
    }

    public static function getCategoryPage(int $regionLevel, int $categoryLevel): Page
    {
        return new Page((new Model\Pages())->getByName('region_' . $regionLevel . '_category_' . $categoryLevel));
    }

    public static function get404AdPage(): Page
    {
        return new Page((new Model\Pages())->getByName('404_ad'));
    }

    public static function get404DefaultPage(): Page
    {
        return new Page((new Model\Pages())->getByName('404_default'));
    }
}