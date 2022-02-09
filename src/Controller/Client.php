<?php

namespace Palto\Controller;

use League\Plates\Engine;
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

class Client
{
    private Engine $templatesEngine;

    private Url $url;

    private ?Region $region;

    private ?Category $category;
    private ?Ad $ad;

    public function __construct()
    {
        $this->templatesEngine = new Engine(Directory::getRootDirectory() . '/templates/laspot');
        $this->templatesEngine->loadExtension(new Translate());
        $this->url = new Url();
        $this->region = Regions::getByUrl($this->url->getRegionUrl());
        $this->category = \Palto\Categories::getByUrl($this->url->getCategoryUrl(), $this->url->getCategoryLevel());
        $this->ad = Ads::getById($this->url->getAdId());
        $this->templatesEngine->addData([
            'region' => $this->region,
            'category' => $this->category,
            'ad' => $this->ad,
            'flash' => Flash::receive(),
            'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category),
        ]);
    }

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
                : [],
            'categories' => $isHot
                ? \Palto\Categories::getLiveCategoriesWithChildren(
                    null,
                    null,
                    Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_1'),
                    Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2')
                )
                : \Palto\Categories::getLiveCategoriesWithChildren()
        ]);
        echo $this->templatesEngine->make($isHot ? 'hot' : 'index');
    }

    public function showRegion($regionUrl, $pageNumber)
    {
        if ($this->region) {
            $this->templatesEngine->addData([
                'title' => 'TITLE',
                'description' => 'DESCRIPTION',
                'h1' => $this->translate('list_h1'),
                'ads' => Ads::getAds($this->region, null),
                'pager' => new Pager($this->region, null, max($pageNumber, 1)),
            ]);
            echo $this->templatesEngine->make('list');
        } else {
            $this->showNotFound();
        }
    }

    public function showCategory()
    {
        if ($this->region && $this->category) {
            $this->templatesEngine->addData([
                'title' => 'TITLE',
                'description' => 'DESCRIPTION',
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

    public function showAd()
    {
        if ($this->region && $this->category && $this->ad) {
            $this->templatesEngine->addData([
                'title' => $this->translate('ad_title'),
                'description' => Filter::shortText($this->ad->getText()),
                'ad' => $this->ad,
                'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category)
            ]);
            echo $this->templatesEngine->make('ad');
        } else {
            $this->showNotFound();
        }
    }

    public function showNotFound()
    {
        header('HTTP/1.1 404 Not Found');
        Debug::dump('Not');
    }
    
    private function translate(string $translate): string
    {
        return Translates::removeExtra(
            Translates::replacePlaceholders(
                Translates::get($translate),
                $this->region,
                $this->category,
                $this->ad,
            )
        );
    }
}