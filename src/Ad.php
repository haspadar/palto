<?php

namespace Palto;

class Ad
{
    private array $ad;
    private array $images;
    private array $details;
    private Category $category;
    private Region $region;

    public function __construct(array $ad, array $images, array $details)
    {
        $this->ad = $ad;
        $this->images = $images;
        $this->details = $details;
        $this->region = Regions::getById($this->getRegionId());
        $this->category = Category::getById($this->getCategoryId());
    }

    public function getTitle(): string
    {
        return $this->ad['title'];
    }

    public function getCurrency(): string
    {
        return $this->ad['currency'];
    }

    public function getPrice(): string
    {
        return $this->ad['price'];
    }

    public function getText(): string
    {
        return $this->ad['text'];
    }

    public function getCategory(): Category
    {
        return $this->category;
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

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public function getUrl(): string
    {
        return $this->ad['url'];
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
        return $this->ad['category_id'];
    }

    private function getRegionId(): int
    {
        return $this->ad['region_id'];
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
}