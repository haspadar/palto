<?php
namespace Palto\Router;

use Palto\Debug;

abstract class Router
{
    protected string $layoutName = NOT_FOUND_LAYOUT;
    protected int $pageNumber = 1;
    protected array $queryParams = [];
    protected string $path;
    protected string $regionUrl = '';
    protected array $categoriesUrls = [];
    protected int $adId = 0;

    /**
     * @return string
     */
    public function getLayoutName(): string
    {
        return $this->layoutName;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setNotFoundLayout()
    {
        $this->layoutName = NOT_FOUND_LAYOUT;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRegionUrl(): string
    {
        return $this->regionUrl;
    }

    /**
     * @return array
     */
    public function getCategoriesUrls(): array
    {
        return $this->categoriesUrls;
    }

    public function getCategoryUrl(): string
    {
        return $this->categoriesUrls[count($this->categoriesUrls) - 1] ?? '';
    }

    public function getCategoryLevel(): int
    {
        return count($this->categoriesUrls);
    }

    /**
     * @return int
     */
    public function getAdId(): int
    {
        return $this->adId;
    }
}