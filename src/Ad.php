<?php

namespace Palto;

class Ad
{
    private array $ad;
    private array $images;
    private array $details;
    private Category $category;
    private Region $region;
    private ?Synonym $synonym;

    public function __construct(array $ad, array $images, array $details, ?Synonym $synonym = null)
    {
        $this->ad = $ad;
        $this->images = $images;
        $this->details = $details;
        $this->region = $this->getRegionId()
            ? Regions::getById($this->getRegionId())
            : new Region([]);
        if ($this->getCategoryId()) {
            $this->category = Categories::getById($this->getCategoryId());
        }

        $this->synonym = $synonym;
    }

    public function getCategoryPath(): string
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }

        return '';
    }

    public function getTitle(): string
    {
        return html_entity_decode($this->ad['title']);
    }

    public function getCurrency(): string
    {
        return $this->ad['currency'];
    }

    public function getPrice(): string
    {
        return $this->ad['price'];
    }

    public function getText(int $length = 0): string
    {
        $text = html_entity_decode($this->ad['text']);
        if (!$this->isDeleted() && !$length) {
            return $text;
        } elseif (!$this->isDeleted() && $length) {
            return mb_substr($text, 0, $length);
        }

        return 'Deleted';
    }

    public function getCategory(): ?Category
    {
        return $this->category ?? null;
    }

    public function getField(): string
    {
        return $this->ad['field'] ?? '';
    }

    public function getSynonym(): ?Synonym
    {
        return $this->synonym;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function generateUrl(): string
    {
        return $this->getCategory()
            ? $this->getCategory()->generateUrl($this->getRegion()) . '/ad' . $this->getId()
            : $this->getRegion()->generateUrl();
    }

    public function getAddress()
    {
        return $this->ad['address'];
    }

    public function getId(): int
    {
        return $this->ad['id'];
    }

    public function isDeleted(): bool
    {
        return $this->ad['deleted_time'] != null;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return !$this->isDeleted() ? $this->images : [];
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        $parsed = [];
        foreach ($this->details as $detail) {
            $parsed[$detail['field']] = $detail['value'];
        }

        return $parsed;
    }

    public function getUrl(): Url
    {
        return new Url($this->ad['url']);
    }

    public function getSellerPhone(): string
    {
        return $this->ad['seller_phone'];
    }

    public function getSellerName(): string
    {
        return $this->ad['seller_name'];
    }

    public function getSellerPostfix(): string
    {
        return $this->ad['seller_postfix'];
    }

    public function getCoordinates(): string
    {
        return $this->ad['coordinates'];
    }

    private function getCategoryId(): int
    {
        return $this->ad['category_id'] ?: 0;
    }

    private function getRegionId(): int
    {
        return $this->ad['region_id'] ?: 0;
    }

    public function getLatitude()
    {
        return $this->parseCoordinate(0);
    }

    public function getLongitute()
    {
        return $this->parseCoordinate(1);
    }

    private function parseCoordinate(int $partNumber)
    {
        $parts = explode(',', $this->getCoordinates());

        return $parts[$partNumber] ?? '';
    }

    public function getCreateTime(): \DateTime
    {
        return new \DateTime($this->ad['create_time']);
    }

    public function getPostTime(): \DateTime
    {
        return new \DateTime($this->ad['post_time']);
    }
}