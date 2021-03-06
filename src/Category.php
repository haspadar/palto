<?php

namespace Palto;

use Palto\Model\Categories;
use Palto\Model\Synonyms;

class Category
{
    /**
     * @var Category[]
     */
    private array $parents;
    private array $children;
    private array $liveChildren;
    private array $childrenIds;
    private array $category;

    public function __construct(array $category)
    {
        $this->category = $category;
    }

    public function getById(int $categoryId): Category
    {
        $region = (new Categories)->getById($categoryId);

        return new Category($region);
    }

    public function addSynonyms(array $synonyms): array
    {
        $result = [];
        foreach ($synonyms as $synonym) {
            $categoryId = (new Synonyms)->add(['title' => $synonym, 'category_id' => $this->getId()]);
            $result[] = new Synonym((new Synonyms)->getById($categoryId));
        }

        return $result;
    }

    public function getSynonyms(): array
    {
        return array_map(fn($synonym) => new Synonym($synonym), (new Synonyms)->getAll($this->getId()));
    }

    public function getGroupedSynonyms(): string
    {
        $synonyms = $this->getSynonyms();

        return $this->groupSynonyms($synonyms);
    }

    public function groupSynonyms(array $synonyms): string
    {
        return implode(', ', array_map(fn(Synonym $synonym) => $synonym->getTitle(), $synonyms));
    }

    public function getParent(): ?Category
    {
        $parents = $this->getParents();

        return $parents ? $parents[0] : null;
    }

    public function getParents(): array
    {
        if (!isset($this->parents)) {
            $parents = [];
            $category = $this;
            while ($category->getParentId()) {
                $categoryRow = (new Categories)->getById($category->getParentId());
                if ($categoryRow) {
                    $category = new self($categoryRow);
                    $parents[] = $category;
                }
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

    public function toArray(): array
    {
        return $this->category;
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

    public function generateUrl(?Region $region): Url
    {
        $parents = $this->getParents();
        $parts = array_filter(array_merge(
            [$region ? $region->getUrl() : ''],
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
            while ($nextLevelCategoriesIds = (new Categories)->getChildrenIds($nextLevelCategoriesIds, ++$level)) {
                $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
            }

            $this->childrenIds = $childrenIds;
        }

        return $this->childrenIds;
    }

    public function getPath(array $addTitles = []): string
    {
        return implode('/', array_reverse($this->getTitles()));
    }

    public function getTitles(array $addTitles = []): array
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
    public function getLiveChildren(?Region $region, int $limit = 0): array
    {
        if (!isset($this->liveChildren)) {
            $this->liveChildren = array_map(
                fn($category) => new self($category),
                (new Categories)->getLiveCategories($this, $region, $limit)
            );
        }

        return $this->liveChildren;
    }

    /**
     * @return Category[]
     */
    public function getChildren(): array
    {
        if (!isset($this->children)) {
            $this->children = array_map(
                fn($category) => new self($category),
                (new Categories)->getChildren([$this->getId()])
            );
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

    public function isParentsEquals(array $urls): bool
    {
        $parentUrls = array_map(fn(self $category) => $category->getUrl(), $this->getParents());

        return $urls == $parentUrls;
    }

    public function update(array $updates): void
    {
        (new Categories)->update($updates, $this->getId());
    }

    public function remove(): void
    {
        (new Categories)->removeChildren($this->getId());
        (new Categories)->remove($this->getId());
        \Palto\Categories::rebuildTree();
    }

    public function isUndefined(): bool
    {
        return mb_strpos($this->getUrl(), 'undefined') === 0;
    }
}