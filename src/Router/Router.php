<?php
namespace Palto\Router;

use Palto\Url;

abstract class Router
{
    protected string $path;
    private Url $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->url->getPageNumber();
    }

    /**
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->url->getQueryParameters();
    }

    public function getSearchQueryParameter(): string
    {
        return $this->getQueryParameter('query');
    }

    public function getQueryParameter(string $name): string
    {
        $parameters = $this->url->getQueryParameters();

        return $parameters[$name] ?? '';
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