<?php

namespace Palto\Layout;

use Palto\Ad;
use Palto\Ads;
use Palto\Categories;
use Palto\Category;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Region;
use Palto\Regions;
use Palto\Translates;

class Client extends Layout
{
    public function getRegionsLinks(): string
    {
        $region = $this->getRegion();
        $regions = array_merge([$region], $region->getParents());
        $links = [];
        foreach ($regions as $region) {
            $links[] = '<a href="' . $this->generateRegionUrl($region) . '">' . $region->getTitle() . '</a>';
        }

        return implode(',', $links);
    }

    public function translate(string $from): string
    {
        return Translates::get($from, $this);
    }

    public function getSearchQuery(): string
    {
        return $this->dispatcher->getRouter()->getSearchQueryParameter();
    }

    public function getRegion(): ?Region
    {
        return $this->dispatcher->getRegion();
    }

    public function getAd(): ?Ad
    {
        return $this->dispatcher->getAd();
    }

    public function getCategory(): ?Category
    {
        return $this->dispatcher->getCategory();
    }

    public function getBreadcrumbUrls(): array
    {
        $defaultRegion = new Region([]);
        $urls = [[
            'title' => $defaultRegion->getTitle(),
            'url' => $defaultRegion->generateUrl(),
        ]];
        if ($this->getRegion()->generateUrl() != $defaultRegion->generateUrl()) {
            $urls[] = [
                'title' => $this->getRegion()->getTitle(),
                'url' => $this->getRegion()->generateUrl()
            ];
        }

        if ($this->getCategory()) {
            foreach ($this->getCategory()->getParents() as $parentCategory) {
                $urls[] = [
                    'title' => $parentCategory->getTitle(),
                    'url' => $parentCategory->generateUrl($this->getRegion())
                ];
            }

            $urls[] = [
                'title' => $this->getCategory()->getTitle(),
                'url' => $this->getCategory()->generateUrl($this->getRegion())
            ];
        }

        return array_filter(
            $urls,
            fn($url) => $url != $this->getDispatcher()->getRouter()->getUrl()->getPath()
        );
    }

    public function getSimilarAds(int $count = 5): array
    {
        return Ads::getAds(
            $this->getRegion(),
            $this->getCategory(),
            $count,
            0
        );
    }

    public function getHotAds(int $limit): array
    {
        return Ads::getAds(
            $this->getRegion(),
            Categories::getById(Config::get('HOT_LAYOUT_HOT_CATEGORY')),
            $limit,
            ($this->getDispatcher()->getRouter()->getPageNumber() - 1) * $limit
        );
    }

    public function getAds(int $limit = Ads::LIMIT): array
    {
        return Ads::getAds(
            $this->getRegion(),
            $this->getCategory(),
            $limit,
            ($this->getDispatcher()->getRouter()->getPageNumber() - 1) * $limit
        );
    }

    public function generateHtmlTitle(string $prefix = ''): string
    {
        if ($this->getAd()) {
            return $this->getAd()->getTitle()
                . ': '
                . implode(
                    ' - ',
                    array_filter(array_merge(
                        $this->getAd()->getCategory()->getWithParentsTitles(),
                        [$this->getAd()->getAddress()],
                        [$prefix . $this->getRegion()->getTitle()],
                    ))
                );
        } else {
            $categoriesTitle = $this->getCategory()
                ? implode(' - ', $this->getCategory()->getWithParentsTitles())
                : '';

            return ($categoriesTitle ? $categoriesTitle . ' - ' : $categoriesTitle)
                . $prefix
                . $this->getRegion()->getTitle();
        }
    }

    public function generateHtmlDescription(string $prefix = ''): string
    {
        return $prefix
            . implode(
                ' - ',
                array_filter(array_merge(
                    ($this->getCategory() ? $this->getCategory()->getWithParentsTitles() : []),
                    [$this->getRegion()->getTitle()]
                ))
            );
    }

    public function generateRegionUrl(Region $region): string
    {
        return $region->generateUrl();
    }

    public function generateAdUrl(Ad $ad): string
    {
        return $ad->generateUrl();
    }

    public function generateCategoryUrl(Category $category): string
    {
        return $category->generateUrl($this->dispatcher->getRegion());
    }

    public function getPublicDirectory(): string
    {
        return Directory::getPublicDirectory();
    }

    public function getParameter(string $name): string
    {
        return $this->getDispatcher()->getRouter()->getQueryParameter($name);
    }

    /**
     * @return Region[]
     */
    public function getWithAdsRegions(?Region $parentRegion = null, int $limit = 0): array
    {
        return Regions::getWithAdsRegions($parentRegion, $limit);
    }

    /**
     * @return Category[]
     */
    public function getWithAdsCategories(?Category $parentCategory = null, int $count = 0): array
    {
        return Categories::getLiveCategories($parentCategory, $this->getRegion(), $count);
    }
}