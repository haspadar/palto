<?php

namespace Palto\Router;

class Standard extends Router
{
    public function __construct(string $path, string $layoutName, int $pageNumber, array $queryParams)
    {
        $this->layoutName = $layoutName;
        $this->pageNumber = $pageNumber;
        $this->queryParams = $queryParams;
        $this->path = $path;
    }
}