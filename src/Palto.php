<?php

namespace Palto;

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Palto
{
    private static Palto $instance;
    private string $title;
    private string $h1;
    private string $description;
    private string $previousPageUrl = '';
    private string $nextPageUrl = '';
    private \MeekroDB $db;
    private string $regionUrl = 'default';
    private string $categoryUrl = '';
    private int $adId = 0;
    private ?array $region = null;
    private ?array $category = null;
    private ?array $ad = null;
    private string $layoutDirectory = '../layouts/';
    private int $pageNumber = 1;
    private array $env;
    private Logger $logger;
    private string $rootDirectory;
    private string $url;
    private int $adsLimit = 30;

    private int $pagesCount;

    public function __construct($rootDirectory = '', string $url = '')
    {
        $this->initRootDirectory($rootDirectory);
        $this->initUrl($url);
        $dotenv = Dotenv::createImmutable($this->rootDirectory);
        $this->env = $dotenv->load();
        $this->initLogger();
        $this->initDb();
        $this->initRegionUrl();
        $this->initCategoryUrl();
        $this->initAdId();
        $this->initRegion();
        $this->initCategory();
//        $this->initPageNumbers();
        $this->initAd();
        self::$instance = $this;
    }

    public static function getInstance(): Palto
    {
        return self::$instance;
    }

    public function setDefaultRegionUrl(string $regionUrl)
    {
        $this->regionUrl = $regionUrl;
    }

    public function setAdsLimit(int $limit)
    {
        $this->adsLimit = $limit;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getLayout(): string
    {
        $parts = $this->getUrlParts();
        if (!$parts) {
            $layout = 'index.inc';
        } elseif (!$this->categoryUrl && $this->regionUrl && $this->region) {
            $layout = 'region.inc';
        } elseif (!$this->adId && $this->categoryUrl && $this->category) {
            $layout = 'category.inc';
        } elseif ($this->adId && $this->ad) {
            $layout = 'ad.inc';
        } else {
            $layout = '404.inc';
        }

        return $layout;
    }

    public function getCategories(int $parentId, int $level = 0, int $limit = 0, $offset = 0): array
    {
        $query = 'SELECT * FROM categories';
        $values = [];
        if ($parentId || $level) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d_parent_id';
            $values['parent_id'] = $parentId;
        }

        if ($level) {
            $query .= 'level = %d_level';
            $values['level'] = $level;
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit OFFSET %d_offset';
            $values['limit'] = $limit;
            $values['offset'] = $offset;
        }

        return $this->getDb()->query($query, $values);
    }

    public function getRegion(int $regionId): array
    {
        return $this->getDb()->queryFirstRow('SELECT * FROM regions WHERE id = %d', $regionId);
    }

    public function getCategory(int $categoryId): array
    {
        return $this->getDb()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $categoryId);
    }

    public function getRegions(int $parentId, int $level = 0, int $limit = 0, int $offset = 0): array
    {
        $query = 'SELECT * FROM regions';
        $values = [];
        if ($parentId || $level) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d_parent_id';
            $values['parent_id'] = $parentId;
        }

        if ($level) {
            $query .= 'level = %d_level';
            $values['level'] = $level;
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit OFFSET %d_offset';
            $values['limit'] = $limit;
            $values['offset'] = $offset;
        }

        return $this->getDb()->query($query, $values);
    }

    public function getCurrentAd(): ?array
    {
        return $this->ad ?? null;
    }

    public function getAd(int $adId): ?array
    {
        $query = $this->getAdsQuery();
        $ad = $this->getDb()->queryFirstRow($query . ' WHERE a.id = %d', $adId);

        return $this->addAdData($ad);
    }

    public function getPaginationPages(): array
    {
        $sliderPages = array_values(array_filter([
                                                     $this->pageNumber - 1,
                                                     $this->pageNumber,
                                                     $this->pageNumber + 1
                                                 ], function ($pageNumber) {
            return $pageNumber >= 1 && $pageNumber <= $this->pagesCount;
        }));
        $hasLeftDots = $this->pageNumber >= 4;
        $hasRightDots = $this->pagesCount >= 5 && $this->pagesCount - $this->getPageNumber() >= 3;
        if ($hasLeftDots) {
            $urls[] = [
                'title' => 1,
                'url' => $this->getPageUrl(1),
            ];
            $urls[] = [
                'title' => '...',
                'url' => ''
            ];
        }

        foreach ($sliderPages as $sliderPage) {
            $urls[] = [
                'title' => $sliderPage,
                'url' => $this->getPageUrl($sliderPage)
            ];
        }

        if ($hasRightDots) {
            $urls[] = [
                'title' => '...',
                'url' => ''
            ];
            $urls[] = [
                'title' => $this->pagesCount,
                'url' => $this->getPageUrl($this->pagesCount)
            ];
        }

        return $urls;
    }

    public function getPaginationUrls(): array
    {
        $sliderPages = array_values(array_filter([
                                                     $this->pageNumber - 1,
                                                     $this->pageNumber,
                                                     $this->pageNumber + 1
                                                 ], function ($pageNumber) {
            return $pageNumber >= 1 && $pageNumber <= $this->pagesCount;
        }));
        $hasLeftDots = $this->pageNumber >= 4;
        $hasRightDots = $this->pagesCount >= 5 && $this->pagesCount - $this->getPageNumber() >= 3;
        if ($hasLeftDots) {
            $urls[] = [
                'title' => 1,
                'url' => $this->getPageUrl(1),
            ];
            $urls[] = [
                'title' => '...',
                'url' => ''
            ];
        } elseif ($this->pageNumber == 3) {
            $urls[] = [
                'title' => 1,
                'url' => $this->getPageUrl(1)
            ];
        }

        foreach ($sliderPages as $sliderPage) {
            $urls[] = [
                'title' => $sliderPage,
                'url' => $this->pageNumber == $sliderPage
                    ? ''
                    : $this->getPageUrl($sliderPage)
            ];
        }

        if ($hasRightDots) {
            $urls[] = [
                'title' => '...',
                'url' => ''
            ];
            $urls[] = [
                'title' => $this->pagesCount,
                'url' => $this->getPageUrl($this->pagesCount)
            ];
        } elseif ($this->pageNumber == 3) {
            $urls[] = [
                'title' => $this->pagesCount,
                'url' => $this->getPageUrl($this->pagesCount)
            ];
        }

        return $urls;
    }

    public function getAdsCount($categoryId, $regionId): int
    {
        $query = 'SELECT COUNT(*) FROM ads AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
        [$where, $values] = $this->getAdsWhere($categoryId, $regionId);
        $query .= $where;

        return $this->getDb()->queryFirstField($query, $values);
    }

    public function getAds($categoryId, $regionId, int $limit, int $offset = 0): array
    {
        $query = $this->getAdsQuery();
        [$where, $values] = $this->getAdsWhere($categoryId, $regionId);
        $query .= $where;
        $query .= ' LIMIT %d_limit OFFSET %d_offset';
        $values['limit'] = $limit;
        $values['offset'] = $offset;
        $ads = $this->getDb()->query($query, $values);

        return $this->addAdsData($ads);
    }

    public function loadLayout(string $layout)
    {
        require_once $this->layoutDirectory . $layout;
        if ($this->isDebug() && !$this->isCli()) {
            $this->showInfo();
        }
    }

    public function showInfo()
    {
        if (!$this->isCli()) {
            echo '<pre>';
        }

        echo 'Info:' . PHP_EOL;
        print_r([
                    'layout' => $this->getLayout(),
                    'region_url' => $this->getRegionUrl(),
                    'category_url' => $this->getCategoryUrl(),
                    'ad_id' => $this->getAdId(),
                    'page_number' => $this->getPageNumber(),
                    'region' => $this->getCurrentRegion(),
                    'category' => $this->getCurrentCategory(),
                    'ad' => $this->getCurrentAd()
                ]);
    }

    public function getAdsLimit(): int
    {
        return $this->adsLimit;
    }

    public function getAdsOffset(): int
    {
        return ($this->getPageNumber() - 1) * $this->getAdsLimit();
    }

    public function getCurrentRegion(): ?array
    {
        return $this->region ?: [
            'id' => 0,
            'title' => $this->regionUrl,
            'url' => $this->regionUrl,
        ];
    }

    public function getCurrentCategory(): ?array
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getLayoutDirectory(): string
    {
        return $this->layoutDirectory;
    }

    /**
     * @param string $layoutDirectory
     */
    public function setLayoutDirectory(string $layoutDirectory): void
    {
        $this->layoutDirectory = $layoutDirectory;
    }

    private function initDb(): void
    {
        $this->db = new \MeekroDB(
            'localhost',
            $this->env['DB_USER'],
            $this->env['DB_PASSWORD'],
            $this->env['DB_NAME']
        );
        if ($this->isDebug() && !$this->isCli()) {
            $this->getDb()->debugMode();
        }
    }

    public function getPreviousPageUrl(): string
    {
        return $this->previousPageUrl;
    }

    public function getNextPageUrl(): string
    {
        return $this->nextPageUrl;
    }

    public function getNextPageNumber(): int
    {
        return $this->pageNumber + 1;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPreviousPageNumber(): int
    {
        return $this->pageNumber - 1;
    }

    public function getPageUrl(int $pageNumber): string
    {
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1] ?? '';
        if ($this->isPageUrlPart($lastPart)) {
            unset($parts[count($parts) - 1]);
        }

        if ($pageNumber > 1) {
            $parts[] = $pageNumber;
        }

        return '/' . implode('/', $parts);
    }

    /**
     * @return string
     */
    public function getCategoryUrl(): string
    {
        return $this->categoryUrl;
    }

    public function generateAdUrl(array $ad): string
    {
        $category = $this->getCategory($ad['category_id']);

        return $this->generateCategoryUrl($category) . '/ad' . $ad['id'];
    }

    public function generateRegionUrl(?array $region): string
    {
        return '/' . ($this->region['url'] ?? '');
    }

    public function generateCategoryUrl(array $category, ?array $region = null): string
    {
        $parents = $this->getParentCategories($category);

        return '/' . implode(
                '/',
                array_merge(
                    [$region['url'] ?? $this->getCurrentRegion()['url']],
                    array_column($parents, 'url'),
                    [$category['url']]
                )
            );
    }

    /**
     * @return string
     */
    public function getRegionUrl(): string
    {
        return $this->regionUrl;
    }

    public function getPublicDirectory(): string
    {
        return $this->rootDirectory . '/public';
    }

    /**
     * @return int
     */
    public function getAdId(): int
    {
        return $this->adId;
    }

    /**
     * @return null[]|string[]
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    public function initPagination(int $count)
    {
        $this->pagesCount = ceil($count / $this->adsLimit);
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1] ?? '';
        $this->pageNumber = $this->isPageUrlPart($lastPart) ? intval($lastPart) : 1;
        if ($this->pageNumber + 1 <= $this->pagesCount) {
            $this->nextPageUrl = $this->getPageUrl($this->pageNumber + 1);
        }

        if ($this->pageNumber > 1) {
            $this->previousPageUrl = $this->getPageUrl($this->pageNumber - 1);
        }
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return \MeekroDB
     */
    public function getDb(): \MeekroDB
    {
        return $this->db;
    }

    private function getUrlParts(): array
    {
        return array_values(array_filter(explode('/', parse_url($this->url)['path'])));
    }

    public function getChildCategories(array $category): array
    {
        $childrenIds = [];
        $nextLevelCategoriesIds = [$category['id']];
        $level = $category['level'];
        while ($nextLevelCategoriesIds = $this->getChildLevelCategoriesIds($nextLevelCategoriesIds, ++$level)) {
            $childrenIds = array_merge($nextLevelCategoriesIds, $childrenIds);
        }

        return $childrenIds
            ? $this->getDb()->query('SELECT * FROM categories WHERE id IN %ld', $childrenIds)
            : [];
    }

    public function dump($data)
    {
        echo '<pre>';
        var_dump($data);
    }

    public function getParentCategories(array $category): array
    {
        $parents = [];
        while ($category['parent_id'] ?? 0) {
            $category = $this->getCategory($category['parent_id']);
            $parents[] = $category;
        }

        return array_reverse($parents);
    }

    public function getCurrentAdBreadcrumbUrls(): array
    {
        return $this->getAdBreadcrumbUrls($this->getCurrentAd());
    }

    public function generateShortText(string $text, int $length = 100): string
    {
        $short = mb_substr($text, 0, $length);
        if ($short != $text) {
            $short .= '...';
        }

        return $short;
    }

    public function getCurrentCategoryBreadcrumbUrls(): array
    {
        return $this->getCategoryBreadcrumbUrls($this->category['parents'], $this->region);
    }

    public function getCategoryBreadcrumbUrls(array $parentCategories, ?array $region = null): array
    {
        $urls = [];
        foreach ($parentCategories as $parentCategory) {
            $urls[] = [
                'url' => $this->generateCategoryUrl($parentCategory, $region),
                'title' => $parentCategory['title']
            ];
        }

        return $urls;
    }

    public function getAdBreadcrumbUrls(array $ad): array
    {
        $category = $this->getCategory($ad['category_id']);
        $categories = $this->getParentCategories($category);
        $region = $this->getRegion($ad['region_id']);

        return $this->getCategoryBreadcrumbUrls(array_merge($categories, [$category]), $region);
    }

    private function initRootDirectory(string $rootDirectory)
    {
        if ($rootDirectory) {
            $this->rootDirectory = $rootDirectory;
        } elseif ($this->isCli()) {
            $this->rootDirectory = $_SERVER['PWD'] ?? '';
        } else {
            $this->rootDirectory = dirname($_SERVER['DOCUMENT_ROOT']);
        }
    }

    private function initUrl(string $url)
    {
        if ($url) {
            $this->url = $url;
        } else {
            $this->url = $_SERVER['REQUEST_URI'] ?? '/';
        }
    }

    private function isPageUrlPart(string $urlPart): bool
    {
        return is_numeric($urlPart);
    }

    private static function isAdUrlPart(string $urlPart): bool
    {
        return substr($urlPart, 0, 2) == 'ad';
    }

    private function getChildLevelCategoriesIds(array $categoriesIds, int $level)
    {
        return $this->getDb()->queryFirstColumn(
            'SELECT id FROM categories WHERE parent_id IN %ld AND level = %d',
            $categoriesIds,
            $level
        );
    }

    private function initAdId()
    {
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1] ?? '';
        if ($this->isAdUrlPart($lastPart)) {
            $this->adId = intval(substr($lastPart, 2));
        }
    }

    private function initAd()
    {
        if ($this->adId) {
            $this->ad = $this->getAd($this->adId);
        }
    }

    private function initRegionUrl()
    {
        $parts = $this->getUrlParts();
        $this->regionUrl = $parts[0] ?? $this->regionUrl;
    }

    private function initRegion()
    {
        if ($this->regionUrl) {
            $this->region = $this->db->queryFirstRow('SELECT * FROM regions WHERE url = %s', $this->regionUrl);
            if ($this->region) {
                $this->region['parents'] = $this->getParentRegions($this->region);
            }
        }
    }

    private function initCategoryUrl()
    {
        $parts = $this->getUrlParts();
        if (count($parts) >= 2) {
            $lastPart = $parts[count($parts) - 1] ?? '';
            if ($this->isPageUrlPart($lastPart) || $this->isAdUrlPart($lastPart)) {
                $this->categoryUrl = $parts[count($parts) - 2] ?? '';
            } else {
                $this->categoryUrl = $lastPart;
            }
        }
    }

    private function initCategory()
    {
        if ($this->categoryUrl) {
            $this->category = $this->db->queryFirstRow('SELECT * FROM categories WHERE url = %s', $this->categoryUrl);
            if ($this->category) {
                $this->category['parents'] = $this->getParentCategories($this->category);
                $this->category['children'] = $this->getChildCategories($this->category);
            }
        }
    }

    private function initLogger()
    {
        $this->logger = new Logger('palto');
        $this->logger->pushHandler(new StreamHandler($this->getEnv()['LOG_PATH']));
    }

    private function addAdsData(array $ads): array
    {
        foreach ($ads as &$ad) {
            $ad = $this->addAdData($ad);
        }

        return $ads;
    }

    private function addAdData(?array $ad): ?array
    {
        if ($ad) {
            $ad['images'] = $this->getAdImages($ad['id']);
        }

        return $ad;
    }

    private function getAdImages(int $adId): array
    {
        return $this->getDb()->query('SELECT big, small FROM ads_images WHERE ad_id = %d', $adId);
    }

    /**
     * @return string
     */
    private function getAdsQuery(): string
    {
        return 'SELECT a.*, c.title AS category_title, c.parent_id AS category_parent_id, c.level AS category_level,'
            . ' c.url AS category_url, r.title AS region_title, r.parent_id AS parent_region_id,'
            . ' r.level AS region_level, r.url AS region_url'
            . ' FROM ads AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
    }

    private function getAdsWhere($categoryId, int $regionId): array
    {
        $query = '';
        $values = [];
        if ($categoryId || $regionId) {
            $query .= ' WHERE ';
            $values['category'] = $categoryId;
            if ($categoryId && is_array($categoryId)) {
                $query .= 'a.category_id IN %ld_category';
            } elseif ($categoryId) {
                $query .= 'a.category_id = %d_category';
            }

            $values['region'] = $regionId;
            if ($regionId && is_array($regionId)) {
                $query .= ' AND a.region_id IN %ld_region';
            } elseif ($regionId) {
                $query .= ' AND a.region_id = %d_region';
            }
        }

        return [$query, $values];
    }

    /**
     * @return bool
     */
    private function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    private function isDebug(): bool
    {
        return $this->getEnv()['DEBUG'] || ($_GET['debug'] ?? 0);
    }

    private function getParentRegions(array $region): array
    {
        $parents = [];
        while ($region['parent_id'] ?? 0) {
            $region = $this->getRegion($region['parent_id']);
            $parents[] = $region;
        }

        return array_reverse($parents);
    }
}