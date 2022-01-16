<?php
namespace Palto\Dispatcher;

use Palto\Ad;
use Palto\Auth;
use Palto\Category;
use Palto\Cli;
use Palto\Config;
use Palto\ExecutionTime;
use Palto\Flash;
use Palto\IP;
use Palto\Layouts;
use Palto\Model\Regions;
use Palto\Region;
use Palto\Router;

class Client extends Dispatcher
{
    private ?Region $region = null;
    private ?Category $category = null;
    private ?Ad $ad = null;

    public function run()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        if (Config::get('AUTH') && !IP::isLocal()) {
            Auth::check();
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } elseif (Config::isDebug()) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
            Regions::getDb()->query('SET SESSION query_cache_type=0;');
        } elseif (Config::withErrors()) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
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
        $layout = Layouts::create($this);
        $layout->load();
        $executionTime->end();
        if (Config::isDebug() && !Cli::isCli()) {
            $this->showInfo($executionTime);
        }
    }

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

    private function showInfo(ExecutionTime $executionTime)
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
            'ad' => $this->ad,
            'execution_time' => $executionTime->get()
        ]);
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