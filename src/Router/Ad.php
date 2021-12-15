<?php

namespace Palto\Router;

class Ad extends Router
{
    public function __construct(string $path, string $regionUrl, array $categoriesUrls, int $adId, array $queryParams)
    {
        $this->layoutName = AD_LAYOUT;
        $this->queryParams = $queryParams;
        $this->path = $path;
        $this->regionUrl = $regionUrl;
        $this->categoriesUrls = $categoriesUrls;
        $this->adId = $adId;
    }
}