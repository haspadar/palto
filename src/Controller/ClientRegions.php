<?php

namespace Palto\Controller;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Palto\Ad;
use Palto\Ads;
use Palto\Breadcrumbs;
use Palto\CategoriesRegionsWithAds;
use Palto\Category;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Filter;
use Palto\Flash;
use Palto\Pager;
use Palto\Plates\Extension\Translate;
use Palto\Region;
use Palto\Regions;
use Palto\Translates;
use Palto\Url;

class ClientRegions extends Client
{
    public function __construct()
    {
        parent::__construct();
        $this->category = \Palto\Categories::getLiveCategories()[0];
    }

    public function showRegion()
    {
        if ($this->region) {
            $this->templatesEngine->addData([
                'title' => $this->translate('list_title'),
                'description' => $this->translate('list_description'),
                'h1' => $this->translate('list_h1'),
                'ads' => Ads::getAds($this->region, $this->category),
                'pager' => new Pager($this->region, $this->category, max($this->url->getPageNumber(), 1)),
                'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category)
            ]);
            echo $this->templatesEngine->make('list');
        } else {
            $this->showNotFound();
        }
    }
}