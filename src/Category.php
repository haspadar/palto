<?php

namespace Palto;

use Palto\Model\Categories;

class Category
{
    /**
     * @var Category[]
     */
    private static array $parents;
    private static array $children;
    private static array $childrenIds;

    private array $category;

    public function __construct(array $category)
    {
        $this->category = $category;
    }

    public static function getById(int $categoryId)
    {
        $region = \Palto\Model\Categories::getById($categoryId);

        return new Category($region);
    }

    public function getParents(): array
    {
        if (!isset(self::$parents)) {
            $parents = [];
            $category = $this;
            while ($category->getParentId()) {
                $category = new self(Categories::getCategory($category->getParentId()));
                $parents[] = $category;
            }

            self::$parents = array_reverse($parents);
        }

        return self::$parents;
    }

    public function getParentId(): int
    {
        return $this->category['parent_id'] ?? 0;
    }

    public function getLevel(): int
    {
        return $this->category['level'] ?? 0;
    }

    public function getId(): int
    {
        return $this->category['id'] ?? 0;
    }

    public function getIconText(): string
    {
        return $this->category['icon_text'] ?? '';
    }

    public function getIconUrl(): string
    {
        return $this->category['icon_url'] ?? '';
    }

    public function getTitle(): string
    {
        return $this->category['title'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->category['url'];
    }

    public function generateUrl(Region $region): Url
    {
        $parents = $this->getParents();
        $parts = array_filter(array_merge(
            [$region->getUrl()],
            array_map(fn (Category $category) => $category->getUrl(), $parents),
            [$this->getUrl()]
        ));

        return new Url('/' . implode('/', $parts));
    }

    public function getChildrenIds(): array
    {
        if (!isset(self::$childrenIds)) {
            $childrenIds = [];
            $nextLevelCategoriesIds = [$this->getId()];
            $level = $this->getLevel();
            while ($nextLevelCategoriesIds = Categories::getChildLevelCategoriesIds($nextLevelCategoriesIds, ++$level)) {
                $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
            }

            self::$childrenIds = $childrenIds;
        }

        return self::$childrenIds;
    }

    public function getWithParentsTitles(array $addTitles = []): array
    {
        $parentsTitles = array_reverse(
            array_map(
                fn (Category $category) => $category->getTitle(),
                $this->getParents()
            )
        );

        return array_filter(
            array_merge(
                [$this->getTitle()],
                $parentsTitles,
                $addTitles
            )
        );
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