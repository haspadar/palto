<?php

namespace Palto\Router;

class Category extends Router
{
    public function __construct(string $path, string $regionUrl, array $categoriesUrls, int $pageNumber, array $queryParams)
    {
        $this->layoutName = LIST_LAYOUT;
        $this->pageNumber = $pageNumber;
        $this->queryParams = $queryParams;
        $this->path = $path;
        $this->regionUrl = $regionUrl;
        $this->categoriesUrls = $categoriesUrls;
    }
}