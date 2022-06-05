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

    public function getId(): int
    {
        return $this->synonym['id'];
    }

    public function getWordsCount(): int
    {
        return count(explode(' ', $this->synonym['title']));
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