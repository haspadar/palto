<?php

namespace Palto\Router;

class Region extends Router
{
    public function __construct(string $path, string $regionUrl, int $pageNumber, array $queryParams)
    {
        $this->layoutName = LIST_LAYOUT;
        $this->pageNumber = $pageNumber;
        $this->queryParams = $queryParams;
        $this->path = $path;
        $this->regionUrl = $regionUrl;
    }
}