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
    private string $regionUrl = '';
    private string $categoryUrl = '';
    private int $adId = 0;
    private ?array $region;
    private ?array $category;
    private ?array $ad;
    private string $layoutDirectory = '../layouts/';
    private int $pageNumber = 1;
    private array $env;
    private Logger $logger;

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
        $this->initPageNumbers();
        $this->initAd();
        self::$instance = $this;
        if ($this->isDebug()) {
            $this->showInfo();
        }
    }

    public static function getInstance(): Palto
    {
        return self::$instance;
    }

    public function getLayout(): string
    {
        if ($this->url == '/') {
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

    public function getCategories(int $parentId, int $level = 0): array
    {
        $query = 'SELECT * FROM categories';
        $values = [];
        if ($parentId || $level) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d';
            $values[] = $parentId;
        }

        if ($level) {
            $query .= 'level = %d';
            $values[] = $level;
        }

        return $this->getDb()->query($query, $values);
    }

    public function getRegion(int $regionId): array
    {
        return $this->getDb()->query('SELECT * FROM regions WHERE id = %d', $regionId);
    }

    public function getCategory(int $categoryId): array
    {
        return $this->getDb()->query('SELECT * FROM categorues WHERE id = %d', $categoryId);
    }

    public function getRegions(int $parentId, int $level = 0): array
    {
        $query = 'SELECT * FROM regions';
        $values = [];
        if ($parentId || $level) {
            $query .= ' WHERE ';
        }

        if ($parentId) {
            $query .= 'parent_id = %d';
            $values[] = $parentId;
        }

        if ($level) {
            $query .= 'level = %d';
            $values[] = $level;
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

    public function getPaginationUrls(int $pagesCount): array
    {
        $urls = [];
        if ($this->getPageNumber() > 2) {
            $urls[1] = $this->getPageUrl(1);
        }

        if ($this->getPageNumber() > 3) {
            $urls[2] = '';
        }

        if ($this->getPageNumber() > 1) {
            $urls[$this->getPageNumber() - 1] = $this->getPageUrl($this->getPageNumber() - 1);
        }

        $urls[$this->getPageNumber()] = $this->getPageUrl($this->getPageNumber());
        if ($pagesCount > $this->getPageNumber()) {
            $urls[$this->getPageNumber() + 1] = $this->getPageUrl($this->getPageNumber() + 1);
        }

        if ($pagesCount - $this->getPageNumber() > 2) {
            $urls[$pagesCount - 1] = '';
        }

        if ($pagesCount - $this->getPageNumber() > 1) {
            $urls[$pagesCount] = $this->getPageUrl($pagesCount);
        }

        return $urls;
    }

    public function getAdsCount(int|array $categoryId, int|array $regionId): count
    {
        $query = 'SELECT COUNT(*) FROM ads AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
        [$where, $values] = $this->getAdsWhere($categoryId, $regionId);
        $query .= $where;

        return $this->getDb()->queryFirstField($query, $values);
    }

    public function getAds(int|array $categoryId, int|array $regionId, int $limit, int $offset = 0): array
    {
        $query = $this->getAdsQuery();
        [$where, $values] = $this->getAdsWhere($categoryId, $regionId);
        $query .= $where;
        $query .= ' LIMIT %d OFFSET %d';
        $values[] = $limit;
        $values[] = $offset;
        $ads = $this->getDb()->query($query, $values);

        return $this->addAdsData($ads);
    }

    public function loadLayout(string $layout)
    {
        require_once $this->layoutDirectory . $layout;
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

    public function getCurrentRegion(): ?array
    {
        return $this->region;
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
        if ($this->isDebug()) {
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
        $parts[count($parts) - 1] = $pageNumber;

        return '/' . implode('/', $parts);
    }

    /**
     * @return string
     */
    public function getCategoryUrl(): string
    {
        return $this->categoryUrl;
    }

    /**
     * @return string
     */
    public function getRegionUrl(): string
    {
        return $this->regionUrl;
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
        return array_values(array_filter(explode('/', $this->url)));
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
        $this->regionUrl = $parts[0] ?? '';
    }

    private function initRegion()
    {
        if ($this->regionUrl) {
            $this->region = $this->db->queryFirstRow('SELECT * FROM regions WHERE url = %s', $this->regionUrl);
        }
    }

    private function initPageNumbers()
    {
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1] ?? '';
        if ($this->isPageUrlPart($lastPart)) {
            $this->pageNumber = $lastPart;
            $this->nextPageUrl = $this->getPageUrl($this->pageNumber + 1);
            if ($this->pageNumber > 1) {
                $this->previousPageUrl = $this->getPageUrl($this->pageNumber - 1);
            }
        }
    }

    private function initCategoryUrl()
    {
        $parts = $this->getUrlParts();
        if (count($parts) > 2) {
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

    private function getAdsWhere(int $categoryId, int $regionId): array
    {
        $query = '';
        $values = [];
        if ($categoryId || $regionId) {
            $query .= ' WHERE ';
            $values[] = $categoryId;
            if ($categoryId && is_array($categoryId)) {
                $query .= 'a.category_id IN %ld';
            } elseif ($categoryId) {
                $query .= 'a.category_id = %d';
            }

            $values[] = $regionId;
            if ($regionId && is_array($regionId)) {
                $query .= 'a.region_id IN %ld';
            } elseif ($regionId) {
                $query .= 'a.region_id = %d';
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
        return $this->getEnv()['DEBUG'] ? true : false;
    }
}