<?php

namespace Palto;

use Palto\Model\Ads;
use Palto\Model\Categories;
use Palto\Router\Router;

class Layout
{
    private string $name;

    private array $partialVariables;
    private Dispatcher $dispatcher;

    public function __construct(string $name, Dispatcher $dispatcher)
    {
        $this->name = $name;
        $this->dispatcher = $dispatcher;
    }

    public function load()
    {
        require_once Directory::getLayoutsDirectory() . '/' . $this->name;
    }

    public function partial(string $file, array $variables = [])
    {
        $this->partialVariables = $variables;
        require Directory::getLayoutsDirectory() . '/partials/' . $file;
    }

    public function getPartialVariable(string $name)
    {
        return $this->partialVariables[$name] ?? '';
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

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function getBreadcrumbUrls()
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
            fn ($url) => $url != $this->getDispatcher()->getRouter()->getUrl()->getPath()
        );
    }

    public function getSimilarAds(int $count = 5): array
    {
        return \Palto\Ads::getAds(
            $this->getRegion() ? $this->getRegion()->getWithChildrenIds() : [],
            $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [],
            $count,
            0
        );
    }

    public function getAds(): array
    {
        return \Palto\Ads::getAds(
            $this->getRegion() ? $this->getRegion()->getWithChildrenIds() : [],
            $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [],
            \Palto\Ads::LIMIT,
            ($this->getDispatcher()->getRouter()->getPageNumber() - 1) * \Palto\Ads::LIMIT
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

    public function generateCategoryUrl(Category $category)
    {
        return $category->generateUrl($this->dispatcher->getRegion());
    }

    public function getPublicDirectory(): string
    {
        return Directory::getPublicDirectory();
    }

    /**
     * @param int $parentRegionId
     * @return Region[]
     */
    public function getWithAdsRegions(int $parentRegionId = 0): array
    {
        return Regions::getWithAdsRegions($parentRegionId);
    }

    /**
     * @return Category[]
     */
    public function getWithAdsCategories(int $parentCategoryId = 0, $level = 1): array
    {
        return \Palto\Categories::getWithAdsCategories($parentCategoryId, $level);
    }
}