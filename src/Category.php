<?php

namespace Palto;

use Palto\Model\Categories;

class Category
{
    /**
     * @var Category[]
     */
    private array $parents;
    private array $children;
    private array $childrenIds;
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
        if (!isset($this->parents)) {
            $parents = [];
            $category = $this;
            while ($category->getParentId()) {
                $category = new self(Categories::getById($category->getParentId()));
                $parents[] = $category;
            }

            $this->parents = array_reverse($parents);
        }

        return $this->parents;
    }

    public function getParentId(): int
    {
        return $this->category['parent_id'] ?? 0;
    }

    public function getLevel(): int
    {
        return $this->category['level'] ?? 1;
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
        if (!isset($this->childrenIds)) {
            $childrenIds = [];
            $nextLevelCategoriesIds = [$this->getId()];
            $level = $this->getLevel();
            while ($nextLevelCategoriesIds = Categories::getChildLevelCategoriesIds($nextLevelCategoriesIds, ++$level)) {
                $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
            }

            $this->childrenIds = $childrenIds;
        }

        return $this->childrenIds;
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
        if (!isset($this->children)) {
            $childrenIds = $this->getChildrenIds();
            $this->children = Categories::getCategoriesByIds($childrenIds);
        }

        return $this->children;
    }

    public function getTreeId(): int
    {
        return $this->category['tree_id'] ?? 0;
    }

    public function getDonorUrl(): Url
    {
        return new Url($this->category['donor_url']);
    }

    public function getEmoji(): string
    {
        return $this->category['emoji'] ?? '';
    }
}