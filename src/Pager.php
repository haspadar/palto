<?php

namespace Palto;

class Pager
{
    private string $nextPageUrl = '';

    private string $previousPageUrl = '';

    private Url $url;

    public function __construct(Region $region, ?Category $category, int $pageNumber)
    {
        $this->url = new Url();
        $offset = $pageNumber * Ads::LIMIT;
        $nextPageAds =  \Palto\Ads::getAds(
            $region,
            $category,
            1,
            $offset
        );
        $hasNextPage = count($nextPageAds) > 0;
        if ($hasNextPage) {
            $this->nextPageUrl = $this->getPageUrl($pageNumber + 1);
        }

        if ($pageNumber > 1) {
            $this->previousPageUrl = $this->getPageUrl($pageNumber - 1);
        }
    }

    private function getPageUrl(int $pageNumber): string
    {
        $withoutPageNumberPath = $this->url->getPath();

        return $withoutPageNumberPath
            . ($pageNumber > 1 ? '/' . $pageNumber : '')
            . ($this->url->getQueryParameters()
                ? '?' . http_build_query($this->url->getQueryParameters())
                : ''
            );
    }

    public function getPageNumber(): int
    {
        return $this->url->getPageNumber();
    }

    /**
     * @return string
     */
    public function getPreviousPageUrl(): string
    {
        return $this->previousPageUrl;
    }

    /**
     * @return string
     */
    public function getNextPageUrl(): string
    {
        return $this->nextPageUrl;
    }
}