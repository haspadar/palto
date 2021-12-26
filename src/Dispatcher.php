<?php
namespace Palto;

use Palto\Model\Regions;
use Palto\Router;

class Dispatcher
{
    private Router\Router $router;
    private ?Region $region = null;
    private ?Category $category = null;
    private ?Ad $ad = null;

    public function __construct(Router\Router $router)
    {
        $this->router = $router;
    }

    public function run()
    {
        if (Config::get('AUTH') && !IP::isLocal()) {
            Auth::check();
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } elseif (Config::isDebug()) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
            Regions::getDb()->query('SET SESSION query_cache_type=0;');
        }

        $regionUrl = $this->getRouter()->getRegionUrl();
        $this->region = \Palto\Regions::getByUrl($regionUrl);
        if ($categoryUrl = $this->getRouter()->getCategoryUrl()) {
            $this->category = \Palto\Categories::getByUrl($categoryUrl, $this->getRouter()->getCategoryLevel());
        }

        if ($adId = $this->getRouter()->getAdId()) {
            $this->ad = \Palto\Ads::getById($adId);
        }

        $this->checkPageExists();
        $layout = new Layout($this->getRouter()->getLayoutName(), $this);
        $layout->load();
        if (Config::isDebug() && !Cli::isCli()) {
            $this->showInfo();
        }
    }
//
//    public function getProjectName(): string
//    {
//        $pathParts = explode('/', Directory::getRootDirectory());
//
//        return $pathParts[count($pathParts) - 1];
//    }

    /**
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @return Ad|null
     */
    public function getAd(): ?Ad
    {
        return $this->ad;
    }
//
//
//    private function initRegion()
//    {
//        if ($this->getRouter()->getRegionUrl() == $this->defaultRegionUrl
//            || !$this->getRouter()->getRegionUrl()
//        ) {
//            $this->region = $this->getDefaultRegion();
//        } else {
//            $this->region = $this->db->queryFirstRow('SELECT * FROM regions WHERE url = %s', $this->getRouter()->getRegionUrl());
//            if ($this->region) {
//                $this->region['parents'] = $this->getParentsRegions($this->region);
//            } else {
//                Flash::add('Region not found.');
//                $this->redirect($this->generateRegionUrl($this->getDefaultRegion()));
//                $this->getRouter()->setNotFoundLayout();
//            }
//        }
//    }
//
//    private function initCategory()
//    {
//        if ($this->getRouter()->getCategoryUrl()) {
//            $this->category = $this->getUrlCategory($this->getRouter()->getCategoryUrl(), $this->getRouter()->getCategoryLevel());
//            if ($this->category) {
//                $this->category['parents'] = $this->getParentCategories($this->category);
//                $this->category['titles'] = array_filter(
//                    array_merge(
//                        [$this->getCurrentCategory()['title']],
//                        array_column(
//                            array_reverse($this->category['parents']),
//                            'title'
//                        ),
//                    )
//                );
//                $this->category['children'] = $this->getChildCategories($this->category);
//            } elseif ($this->region) {
//                Flash::add('Category not found.');
//                $this->redirect($this->generateRegionUrl($this->region));
//                $this->getRouter()->setNotFoundLayout();
//            } else {
//                $this->getRouter()->setNotFoundLayout();
//            }
//        }
//    }

    private function showInfo()
    {
        echo '<pre>Info:' . PHP_EOL;
        print_r([
            'layout' => $this->getRouter()->getLayoutName(),
            'region_url' => $this->getRouter()->getRegionUrl(),
            'category_url' => $this->getRouter()->getCategoryUrl(),
            'categories_urls' => $this->getRouter()->getCategoriesUrls(),
            'ad_id' => $this->getRouter()->getAdId(),
            'page_number' => $this->getRouter()->getPageNumber(),
            'region' => $this->region,
            'category' => $this->category,
            'ad' => $this->ad
        ]);
    }
//
//    private function initAd()
//    {
//        if ($this->getRouter()->getAdId()) {
//            $this->ad = $this->getAd($this->getRouter()->getAdId());
//            if (!$this->ad) {
//                Flash::add('The ad was deleted.');
//                if ($this->category) {
//                    $this->redirect($this->generateCategoryUrl($this->category));
//                }
//
//                $this->getRouter()->setNotFoundLayout();
//            }
//        }
//    }

    /**
     * @return Router\Router
     */
    public function getRouter(): Router\Router
    {
        return $this->router;
    }

    private function redirect(string $categoryUrl)
    {
        header("Location: " . $categoryUrl,true,301);
    }

    private function checkPageExists()
    {
        if ($this->getRouter() instanceof Router\Ad && !$this->ad) {
            Flash::add('The ad was deleted.');
            $redirectUrl = $this->router->getUrl()->generateParentUrl();
        } elseif ($this->getRouter() instanceof Router\Category && !$this->category) {
            Flash::add('The category was deleted.');
            $redirectUrl = $this->router->getUrl()->generateParentUrl();
        } elseif ($this->getRouter() instanceof Router\Region && !$this->region) {
            Flash::add('The region was deleted.');
            $redirectUrl = $this->router->getUrl()->generateParentUrl();
        }

        if (isset($redirectUrl) && $redirectUrl->getPath() != $this->getRouter()->getUrl()->getPath()) {
            $this->redirect($redirectUrl->getFull());
        }
    }
}