<?php
namespace Palto\Router;

use Palto\Url;

abstract class Router
{
    protected string $layoutName = NOT_FOUND_LAYOUT;
    protected array $queryParams = [];
    protected string $path;
    private Url $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

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
        return $this->url->getPageNumber();
    }

    public function setNotFoundLayout()
    {
        $this->layoutName = NOT_FOUND_LAYOUT;
    }

    /**
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->queryParams;
    }

    public function getSearchQueryParameter(): string
    {
        return $this->getQueryParameter('query');
    }

    public function getQueryParameter(string $name): string
    {
        return $this->queryParams[$name] ?? '';
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
        return $this->url->getRegionUrl();
    }

    /**
     * @return array
     */
    public function getCategoriesUrls(): array
    {
        return $this->url->getCategoriesUrls();
    }

    public function getCategoryUrl(): string
    {
        $categoriesUrls = $this->getCategoriesUrls();

        return $categoriesUrls[count($categoriesUrls) - 1] ?? '';
    }

    public function getCategoryLevel(): int
    {
        return count($this->getCategoriesUrls());
    }

    public function getAdId(): int
    {
        return $this->url->getAdId();
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }
}