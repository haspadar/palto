<?php

namespace Palto\Controller;

use Palto\Ads;
use Palto\Breadcrumbs;
use Palto\Pager;

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
                'ads' => Ads::getAds(
                    $this->region,
                    $this->category,
                    Ads::LIMIT,
                    ($this->url->getPageNumber() -1) * Ads::LIMIT
                ),
                'pager' => new Pager($this->region, $this->category, max($this->url->getPageNumber(), 1)),
                'breadcrumbs' => Breadcrumbs::getUrls($this->region, $this->category)
            ]);
            echo $this->templatesEngine->make('list');
        } else {
            $this->showNotFound();
        }
    }
}