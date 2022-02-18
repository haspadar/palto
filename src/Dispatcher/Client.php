<?php
namespace Palto\Dispatcher;

use Palto\Ad;
use Palto\Auth;
use Palto\Category;
use Palto\Cli;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\ExecutionTime;
use Palto\Flash;
use Palto\IP;
use Palto\Layout\Layout;
use Palto\Layouts;
use Palto\Model\Regions;
use Palto\Region;
use Palto\Router;
use Palto\Url;

class Client extends Dispatcher
{
    private ?Region $region = null;
    private ?Category $category = null;
    private ?Ad $ad = null;
    private array $staticLayouts;

    public function run()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $this->checkVisibleErrors();
        $this->staticLayouts = require_once Directory::getRootDirectory() . '/' . Directory::STATIC_LAYOUTS_SCRIPT;
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
        $layout->load($this->getTheme() . '/' . $this->getLayoutName());
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

    public function getTheme(): string
    {
        $foundTheme = '';
        $existThemes = Directory::getThemes();
        foreach ($existThemes as $existTheme) {
            $themeSites = explode(',', Config::get('THEME_' . mb_strtoupper($existTheme) . '_SITES'));
            if (in_array($_SERVER['SERVER_NAME'], $themeSites)) {
                $foundTheme = $existTheme;
                break;
            }
        }

        if (!$foundTheme) {
            $foundTheme = Config::get('THEME_DEFAULT') ?: $existThemes[0];
        }

        return $foundTheme;
    }

    private function showInfo(ExecutionTime $executionTime)
    {
        echo '<pre>Info:' . PHP_EOL;
        print_r([
            'layout' => $this->getLayoutName(),
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

    private function checkVisibleErrors()
    {
        if (Config::get('AUTH') && !IP::isLocal()) {
            Auth::check();
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } elseif (Config::isDebug()) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
            Regions::getConnection()->query('SET SESSION query_cache_type=0;');
        } elseif (Config::withErrors()) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        }
    }

    protected function getLayoutName(): string
    {
        $url = $this->getRouter()->getUrl();
        $staticLayout = $this->getStaticLayout($url);
        if ($staticLayout) {
            $layoutName = 'static/' . $staticLayout;
        } elseif ($url->isAdPage() && $this->ad) {
            $layoutName = Directory::LAYOUT_AD;
        } elseif ($url->isCategoryPage() && $this->getCategory()) {
            $layoutName = Directory::LAYOUT_LIST;
        } elseif (($url->isRegionPage() && $this->getRegion()->getId()) || $url->isDefaultRegionPage()) {
            $layoutName = Directory::LAYOUT_LIST;
        } else {
            $layoutName = Directory::LAYOUT_404;
        }

        return $layoutName;
    }

    private function getStaticLayout(Url $url): string
    {
        $path = $url->getPath();
        if (isset($this->staticLayouts[$path])
            && file_exists(Directory::getLayoutsDirectory()
                . '/'
                . $this->getTheme()
                . '/static/'
                . $this->staticLayouts[$path]
            )
        ) {
            return $this->staticLayouts[$path];
        }

        return '';
    }
}