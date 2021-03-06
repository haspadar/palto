<?php

namespace Palto;

use Cassandra\Set;
use Palto\Model\Categories;
use Palto\Model\Regions;
use Palto\Router\Router;

class Region
{
    private array $region;
    private array $parents;
    private array $children;
    private array $childrenIds;

    public function __construct(array $region = [])
    {
        $this->region = $region ?: [
            'title' => Settings::getByName('default_region_title'),
            'url' => Settings::getByName('default_region_url'),
        ];
    }

    public function toArray(): array
    {
        return $this->region;
    }

    public function getDonorUrl(): Url
    {
        return new Url($this->region['donor_url']);
    }

    public function getTitle(): string
    {
        return $this->region['title'] ?? '';
    }

    public function getAbbreviation(): string
    {
        return $this->region['abbreviation'] ?? '';
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

    public function getParents(): array
    {
        if (!isset($this->parents)) {
            $parents = [];
            $region = $this;
            while ($region->getParentId()) {
                $region = new self((new Regions)->getById($region->getParentId()));
                $parents[] = $region;
            }

            $this->parents = array_reverse($parents);
        }

        return $this->parents;
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
        if (!isset($this->childrenIds)) {
            $childrenIds = [];
            $nextLevelRegionsIds = [$this->getId()];
            while ($nextLevelRegionsIds = (new Regions)->getChildRegionsIds($nextLevelRegionsIds)) {
                $childrenIds = array_merge($nextLevelRegionsIds, $childrenIds);
            }

            $this->childrenIds = $childrenIds;
        }

        return $this->childrenIds;
    }

    public function getWithChildrenIds(): array
    {
        return array_merge(
            [$this->getId()],
            $this->getChildrenIds()
        );
    }

    /**
     * @return Region[]
     */
    public function getChildren(): array
    {
        if (!isset($this->children)) {
            $childrenIds = $this->getChildrenIds();
            $this->children = (new \Palto\Regions())->getByIds($childrenIds);
        }

        return array_map(fn(array $region) => new Region($region), $this->children);
    }

    public function getTreeId(): int
    {
        return $this->region['tree_id'] ?? 0;
    }
}