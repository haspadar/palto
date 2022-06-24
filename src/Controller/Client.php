<?php

namespace Palto\Controller;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Palto\Ad;
use Palto\Ads;
use Palto\Breadcrumbs;
use Palto\Live;
use Palto\Category;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Filter;
use Palto\Flash;
use Palto\Pager;
use Palto\Pages;
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
        $this->templatesEngine = new Engine(Directory::getThemeTemplatesDirectory());
        $this->templatesEngine->loadExtension(new Translate());
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
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
        $page = Pages::getMainPage();
        $isHot = (bool)\Palto\Config::get('HOT_LAYOUT');
        $limit = $isHot ? \Palto\Config::get('HOT_LAYOUT_REGIONS') : Config::get('INDEX_LAYOUT_REGIONS');
        $this->templatesEngine->addData([
            'title' => $this->replaceHtml($page->getTitle()),
            'description' => $this->replaceHtml($page->getDescription()),
            'h1' => $this->replaceHtml($page->getH1()),
            'regions' => !is_numeric($limit) || intval($limit) > 0
                ? Regions::getLiveRegions(null, intval($limit))
                : [],
            'live_categories' => \Palto\Categories::getLiveCategories(null, $this->region),
            'breadcrumbs' => [],
            'page' => $page
        ]);
        echo $this->templatesEngine->make($page->getTemplate()->getShortName());
    }

    public function showRegistration()
    {
        $page = Pages::getRegistrationsPage();
        $this->templatesEngine->addData([
            'title' => $this->replaceHtml($page->getTitle()),
            'description' => $this->replaceHtml($page->getDescription()),
            'h1' => $this->replaceHtml($page->getH1()),
        ]);
        echo $this->templatesEngine->make($page->getTemplate()->getShortName());
    }

    public function showRegionsList()
    {
        $page = Pages::getRegionsPage();
        $this->templatesEngine->addData([
            'title' => $this->replaceHtml($page->getTitle()),
            'description' => $this->replaceHtml($page->getDescription()),
            'h1' => $this->replaceHtml($page->getH1()),
        ]);
        echo $this->templatesEngine->make($page->getTemplate()->getShortName());
    }

    public function showCategoriesList()
    {
        $page = Pages::getCategoriesPage();
        $this->templatesEngine->addData([
            'title' => $this->replaceHtml($page->getTitle()),
            'description' => $this->replaceHtml($page->getDescription()),
            'h1' => $this->replaceHtml($page->getH1()),
        ]);
        echo $this->templatesEngine->make($page->getTemplate()->getShortName());
    }

    public function showRegion($regionUrl, $pageNumber)
    {
        if ($this->region) {
            $page = Pages::getRegionPage($this->region->getLevel());
            Debug::dump($page->getH1(), '$page->getH1()');
            $this->templatesEngine->addData([
                'title' => $this->replaceHtml($page->getTitle()),
                'description' => $this->replaceHtml($page->getDescription()),
                'h1' => $this->replaceHtml($page->getH1()),
                'ads' => Ads::getAds(
                    $this->region,
                    null,
                    Ads::LIMIT,
                    ($this->url->getPageNumber() - 1) * Ads::LIMIT
                ),
                'pager' => new Pager($this->region, null, max($pageNumber, 1)),
            ]);
            echo $this->templatesEngine->make($page->getTemplate()->getShortName());
        } else {
            $this->showNotFound();
        }
    }

    public function showCategory()
    {
        $parentUrls = $this->url->getCategoriesUrls();
        array_pop($parentUrls);
        if ($this->region && $this->category && $this->category->isParentsEquals($parentUrls)) {
            $page = Pages::getCategoryPage($this->region->getLevel(), $this->category->getLevel());
            $this->templatesEngine->addData([
                'title' => $this->replaceHtml($page->getTitle()),
                'description' => $this->replaceHtml($page->getDescription()),
                'h1' => $this->replaceHtml($page->getH1()),
                'ads' => Ads::getAds(
                    $this->region,
                    $this->category,
                    Ads::LIMIT,
                    ($this->url->getPageNumber() - 1) * Ads::LIMIT
                ),
                'pager' => new Pager($this->region, $this->category, max($this->url->getPageNumber(), 1)),
                'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category)
            ]);
            echo $this->templatesEngine->make($page->getTemplate()->getShortName());
        } else {
            $this->showNotFound();
        }
    }

    public function showAd()
    {
        $page = Pages::getAdPage();
        if ($this->category && $this->ad) {
            $this->templatesEngine->addData([
                'title' => $this->replaceHtml($page->getTitle()),
                'description' => $this->replaceHtml($page->getDescription()),
                'h1' => $this->replaceHtml($page->getH1()),
                'ad' => $this->ad,
                'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category)
            ]);
            echo $this->templatesEngine->make($page->getTemplate()->getShortName());
        } else {
            $this->showNotFound();
        }
    }

    public function showNotFound()
    {
        if ($this->url->isAdPage()) {
            $page = Pages::get404AdPage();
        } else {
            $page = Pages::get404DefaultPage();
        }

        header('HTTP/1.1 404 Not Found');
        $this->templatesEngine->addData([
            'title' => $this->replaceHtml($page->getTitle()),
            'description' => $this->replaceHtml($page->getDescription()),
            'h1' => $this->replaceHtml($page->getH1()),
            'h2' => $this->replaceHtml($page->getH2()),
        ]);
        echo $this->templatesEngine->make($page->getTemplate()->getShortName());
    }
    
    private function replaceHtml(string $value): string
    {
        return Translates::removeExtra(
            Translates::replacePlaceholders(
                $value,
                $this->region,
                $this->category,
                $this->ad,
            )
        );
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