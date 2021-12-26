<?php

namespace Palto;

use Palto\Model\Categories;
use Palto\Model\Regions;
use Palto\Router\Router;

class Region
{
    private array $region;

    private static array $parents;
    private static array $children;
    private static array $childrenIds;

    public function __construct(array $region)
    {
        $this->region = $region ?: [
            'title' => Config::get('DEFAULT_REGION_TITLE'),
            'url' => Config::get('DEFAULT_REGION_URL')
        ];
    }

    public function getTitle(): string
    {
        return $this->region['title'] ?? '';
    }

    public function generateUrl(): string
    {
        return '/' . $this->getUrl();
    }

    public function getUrl(): string
    {
        return $this->region['url'] ?? '';
    }

    public function getParentId(): int
    {
        return $this->region['parent_id'] ?? 0;
    }

    public function getLevel(): int
    {
        return $this->region['level'] ?? 0;
    }

    public function getId(): int
    {
        return $this->region['id'] ?? 0;
    }

    public function getChildrenIds(): array
    {
        if (!isset(self::$childrenIds)) {
            $childrenIds = [];
            $nextLevelRegionsIds = [$this->getId()];
            while ($nextLevelRegionsIds = Regions::getChildRegionsIds($nextLevelRegionsIds)) {
                $childrenIds = array_merge($nextLevelRegionsIds, $childrenIds);
            }

            self::$childrenIds = $childrenIds;
        }

        return self::$childrenIds;
    }

    public function getWithChildrenIds(): array
    {
        return array_merge(
            [$this->getId()],
            $this->getChildrenIds()
        );
    }

    /**
     * @return Category[]
     */
    public function getChildren(): array
    {
        if (!isset(self::$children)) {
            $childrenIds = $this->getChildrenIds();
            self::$children = Categories::getCategoriesByIds($childrenIds);
        }

        return self::$children;
    }
}