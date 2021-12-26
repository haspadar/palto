<?php

namespace Palto\Router;

use Palto\Url;

class Standard extends Router
{
    public function __construct(Url $url, string $layoutName)
    {
        parent::__construct($url);
        $this->layoutName = $layoutName;
    }
}