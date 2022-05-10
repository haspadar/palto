<?php

namespace Palto;

class Synonym
{
    private array $synonym;
    private Category $category;

    public function __construct(array $synonym)
    {
        $this->synonym = $synonym;
        $this->category = Categories::getById($synonym['category_id']);
    }

    public function getSpacesCount(): int
    {
        return count(explode(' ', $this->getTitle())) - 1;
    }

    public function getTitle(): string
    {
        return $this->synonym['title'];
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}