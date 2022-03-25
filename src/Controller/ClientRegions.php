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
    public function showIndex()
    {
        $isHot = (bool)\Palto\Config::get('HOT_LAYOUT');
        $limit = $isHot ? \Palto\Config::get('HOT_LAYOUT_REGIONS') : Config::get('INDEX_LAYOUT_REGIONS');
        $this->templatesEngine->addData([
            'title' => $this->translate('index_title'),
            'description' => $this->translate('index_description'),
            'h1' => $isHot ? $this->translate('hot_h1') : $this->translate('index_h1'),
            'regions' => !is_numeric($limit) || intval($limit) > 0
                ? Regions::getWithAdsRegions(null, intval($limit))
                : []
        ]);
        echo $this->templatesEngine->make($isHot ? 'hot' : 'index');
    }

    public function showRegion()
    {
        $parentUrls = $this->url->getRegionsUrls();
        array_pop($parentUrls);
        if ($this->region && $this->category && $this->category->isParentsEquals($parentUrls)) {
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