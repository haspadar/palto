<?php

namespace Palto;

use Dotenv\Dotenv;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pylesos\PylesosService;

class Palto
{
    private string $previousPageUrl = '';
    private string $nextPageUrl = '';
    private \MeekroDB $db;
    private string $defaultRegionUrl = 'default';
    private string $defaultRegionTitle = 'Default';
    private string $regionUrl = '';
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

    private array $partialVariables = [];

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
        $this->initAd();
    }

    public function setDefaultRegionTitle(string $regionTitle)
    {
        $this->defaultRegionTitle = $regionTitle;
    }

    public function setDefaultRegionUrl(string $regionUrl)
    {
        $this->defaultRegionUrl = $regionUrl;
    }

    public function setAdsLimit(int $limit)
    {
        $this->adsLimit = $limit;
    }

    public function getLayout(): string
    {
        $parts = $this->getUrlParts();
        $isRegionPage = !$this->categoryUrl && $this->regionUrl && $this->region;
        $isCategoryPage = !$this->adId && $this->categoryUrl && $this->category;
        if (!$parts) {
            $layout = 'index.php';
        } elseif ($isRegionPage || $isCategoryPage) {
            $layout = 'list.php';
        } elseif ($this->adId && $this->ad) {
            $layout = 'ad.php';
        } else {
            $layout = '404.php';
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

    public function isAdUrlExists(string $url): bool
    {
        $found = $this->getDb()->queryFirstRow('SELECT * FROM ads WHERE url = %s', $url);

        return $found ? true : false;
    }

    public function transformUrl(string $url): string
    {
        return str_replace('/', '_', substr($url, 1, -1));
    }

    public function getRegionId(array $region): int
    {
        $found = $this->getDb()->queryFirstRow('SELECT * FROM regions WHERE url = %s', $region['url']);
        if (!$found) {
            $this->getDb()->insert('regions', $region);
            $this->getLogger()->debug('Added region ' . $region['title']);

            return $this->getDb()->insertId();
        }

        $this->getLogger()->debug('Ignored existing region ' . $region['title']);

        return $found['id'];
    }

    public function getCategoryId(array $category): int
    {
        $found = $this->getDb()->queryFirstRow('SELECT * FROM categories WHERE donor_url = %s', $category['donor_url']);
        if (!$found) {
            $this->getDb()->insert('categories', $category);
            $this->getLogger()->debug('Added category ' . $category['title']);

            return $this->getDb()->insertId();
        }

        $this->getLogger()->debug('Ignored existing category ' . $category['title']);

        return $found['id'];
    }

    public function addAd(array $ad, array $images, array $details): int
    {
        $this->getDb()->insert('ads', $ad);
        $adId = $this->getDb()->insertId();
        foreach ($images as $image) {
            $this->getDb()->insert('ads_images', [
                'small' => $image['small'],
                'big' => $image['big'],
                'ad_id' => $adId,
            ]);
        }

        foreach ($details as $detailField => $detailValue) {
            $fieldId = $this->getDetailsFieldId($ad['category_id'], $detailField);
            $this->getDb()->insert('ads_details', [
                'details_field_id' => $fieldId,
                'ad_id' => $adId,
                'value' => $detailValue
            ]);
        }

        return $adId;
    }

    public function getDetailsFieldId(int $categoryId, string $field): int
    {
        $fieldId = $this->getDb()->queryFirstField(
            'SELECT * FROM details_fields WHERE category_id = %d AND field = %s LIMIT 1',
            $categoryId,
            $field
        );
        if (!$fieldId) {
            $this->getDb()->insert('details_fields', [
                'category_id' => $categoryId,
                'field' => $field
            ]);
            $fieldId = $this->getDb()->insertId();
        }

        return $fieldId;
    }

    public function getAdLastTime(): ?string
    {
        return $this->getDb()->queryFirstField("SELECT MAX(post_time) FROM ads");
    }

    public function getAd(int $adId): ?array
    {
        $query = $this->getAdsQuery();
        $ad = $this->getDb()->queryFirstRow($query . ' WHERE a.id = %d', $adId);

        return $this->addAdData($ad);
    }

    public function getPaginationUrls(): array
    {
        $urls = [];
        if ($this->pagesCount <= 5) {
            for ($pageNumber = 1; $pageNumber <= $this->pagesCount; $pageNumber++) {
                $urls[] = [
                    'title' => $pageNumber,
                    'url' => $this->pageNumber == $pageNumber
                        ? ''
                        : $this->getPageUrl($pageNumber),
                ];
            }
        } else {
            $sliderPages = array_values(array_filter([
                 $this->pageNumber - 1,
                 $this->pageNumber,
                 $this->pageNumber + 1
             ], function ($pageNumber) {
                return $pageNumber >= 1 && $pageNumber <= $this->pagesCount;
            }));
            $hasLeftDots = $this->pageNumber >= 4;
            $hasRightDots = $this->pagesCount - $this->getPageNumber() >= 3;
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
        return $this->region;
    }

    public function getCurrentCategory(): ?array
    {
        return $this->category ?: [
            'id' => 0,
            'title' => '',
            'children' => [],
            'parents' => []
        ];
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
        return '/' . ($region['url'] ?? $this->region['url']);
    }

    public function partial(string $file, array $variables = [])
    {
        $this->partialVariables = $variables;
        require $this->getLayoutDirectory() . '/partials/' . $file;
    }

    public function getPartialVariable(string $name)
    {
        return $this->partialVariables[$name] ?? '';
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
        return $this->getListAdBreadcrumbUrls($this->getCurrentAd());
    }

    public function generateShortText(string $text, int $length = 100): string
    {
        $short = mb_substr($text, 0, $length);
        if ($short != $text) {
            $short .= '...';
        }

        return $short;
    }

    public function getAdBreadcrumbUrls(): array
    {
        return array_merge(
            [[
                'title' => $this->getCurrentRegion()['title'] ?? $this->getCurrentRegion()['title'],
                'url' => $this->generateRegionUrl($this->getCurrentRegion()),
            ]],
            $this->getCategoryBreadcrumbUrls(
                array_merge([$this->getCurrentCategory()], $this->getCurrentCategory()['parents']),
                $this->getCurrentRegion()
            )
        );
    }

    public function getListBreadcrumbUrls(): array
    {
        return array_merge(
            [[
                'title' => $this->getCurrentRegion()['title'] ?? $this->getCurrentRegion()['title'],
                'url' => $this->generateRegionUrl($this->getCurrentRegion()),
            ]],
            $this->getCategoryBreadcrumbUrls($this->getCurrentCategory()['parents'], $this->getCurrentRegion())
        );
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

    public function getListAdBreadcrumbUrls(array $ad): array
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
            $this->rootDirectory = trim(`pwd`);
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
        if ($this->regionUrl == $this->defaultRegionUrl || !$this->regionUrl) {
            $this->region = [
                'id' => 0,
                'title' => $this->defaultRegionTitle,
                'url' => $this->defaultRegionUrl,
            ];
        } else {
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
                unset($parts[count($parts) - 1]);
            }

            unset($parts[0]);
            $parts = array_values($parts);
            if ($parts) {
                $this->categoryUrl = $parts[count($parts) - 1];
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
        $this->logger->pushHandler(new RotatingFileHandler(
            $this->rootDirectory . '/logs/parser',
            20,
            Logger::INFO
        ));
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
            $ad['details'] = $this->getAdDetails($ad['id']);
        }

        return $ad;
    }

    private function getAdDetails(int $adId): array
    {
        return array_column(
            $this->getDb()->query('SELECT field, value FROM details_fields AS df INNER JOIN ads_details AS dfv ON df.id = dfv.details_field_id WHERE ad_id = %d', $adId),
            'value',
            'field'
        );
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
            $where = [];
            if ($categoryId && is_array($categoryId)) {
                $where[] = 'a.category_id IN %ld_category';
            } elseif ($categoryId) {
                $where[] =  'a.category_id = %d_category';
            }

            $values['region'] = $regionId;
            if ($regionId && is_array($regionId)) {
                $where[] = 'a.region_id IN %ld_region';
            } elseif ($regionId) {
                $where[] = 'a.region_id = %d_region';
            }

            $query .= implode(' AND ', $where);
        }

        return [$query, $values];
    }

    private function isCron(): bool
    {
        return $this->isCli() && !isset($_SERVER['TERM']);
    }

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

    public function parseYoutubeVideoId(string $query): int
    {
        $html = PylesosService::download(
            'https://www.youtube.com/results?search_query=' . urlencode($query),
            $this->getEnv()
        )->getResponse();
        $pattern = '/watch?v=';
        $videoUrlStart = strpos($html, '/watch?v=');
        if ($videoUrlStart) {
            $videoUrlFinish = strpos($html, '"', $videoUrlStart);
            $videoId = substr(
                $html,
                $videoUrlStart + strlen($pattern),
                $videoUrlFinish - $videoUrlStart - strlen($pattern)
            );
        }

        return $videoId ?? 0;
    }
}