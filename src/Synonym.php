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
        return $this->synonym['spaces_count'];
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