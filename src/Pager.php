<?php

namespace Palto;

class Pager
{
    private string $nextPageUrl = '';

    private string $previousPageUrl = '';

    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $pageNumber = $dispatcher->getRouter()->getPageNumber();
        $offset = $pageNumber * Ads::LIMIT;
        $nextPageAds =  \Palto\Ads::getAds(
            $dispatcher->getRegion(),
            $dispatcher->getCategory(),
            1,
            $offset
        );
        $hasNextPage = count($nextPageAds) > 0;
        if ($hasNextPage) {
            $this->nextPageUrl = $this->getPageUrl($dispatcher->getRouter()->getPageNumber() + 1);
        }

        if ($pageNumber > 1) {
            $this->previousPageUrl = $this->getPageUrl($dispatcher->getRouter()->getPageNumber() - 1);
        }
    }

    private function getPageUrl(int $pageNumber): string
    {
        $url = $this->dispatcher->getRouter()->getUrl();
        $withoutPageNumberPath = $url->getPath();

        return $withoutPageNumberPath
            . '/'
            . $pageNumber
            . ($this->dispatcher->getRouter()->getQueryParameters()
                ? '?' . http_build_query($this->dispatcher->getRouter()->getQueryParameters())
                : ''
            );
    }

    public function getPageNumber(): int
    {
        return $this->dispatcher->getRouter()->getPageNumber();
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