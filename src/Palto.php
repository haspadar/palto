<?php

namespace Palto;

use Dotenv\Dotenv;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pylesos\PylesosService;

class Palto
{
    public const PARSE_CATEGORIES_SCRIPT = 'parse_categories.php';
    public const PARSE_SINGLE_SITE_ADS_SCRIPT = 'parse_single_site_ads.php';
    public const PARSE_ADS_SCRIPT = 'parse_ads.php';
    private string $previousPageUrl = '';
    private string $nextPageUrl = '';
    private \MeekroDB $db;
    private string $defaultRegionUrl;
    private string $defaultRegionTitle;
    private string $regionUrl = '';
    private array $categoriesUrls = [];
    private string $categoryUrl = '';
    private int $categoryLevel = 0;
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
        $this->initDefaultRegion();
        $this->initRegionUrl();
        $this->initCategoriesUrls();
        $this->initAdId();
        $this->initRegion();
        $this->initCategory();
        $this->initAd();
    }

    public function safeTransaction(Callable $function)
    {
        try {
            $this->getDb()->startTransaction();
            $return = $function();
            $this->getDb()->commit();

            return $return;
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            if (!$this->isCron() && $this->isCli()) {
                exit;
            }
        }
    }

    public function getDomainUrl(): string
    {
        $domainUrl = $this->getEnv()['DOMAIN_URL'];
        if (!$domainUrl) {
            $domainUrl = 'https://www.' . $this->getProjectName();
        }

        return $domainUrl;
    }

    public function getPhpDomainUrl(): string
    {
        $domainUrl = $this->getEnv()['PHP_DOMAIN_URL'];
        if (!$domainUrl) {
            $domainUrl = 'https://php.' . $this->getProjectName();
        }

        return $domainUrl;
    }

    public function getProjectName(): string
    {
        $pathParts = explode('/', $this->getRootDirectory());

        return $pathParts[count($pathParts) - 1];
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
        $isRegistrationPage = isset($parts[0]) && $parts[0] == 'registration';
        $isRegionsListPage = isset($parts[0]) && $parts[0] == 'regions';
        $isCategoriesListPage = isset($parts[0]) && $parts[0] == 'categories';
        if (!$parts) {
            $layout = 'index.php';
        } elseif ($isCategoriesListPage) {
            $layout = 'categories-list.php';
        } elseif ($isRegionsListPage) {
            $layout = 'regions-list.php';
        } elseif ($isRegistrationPage) {
            $layout = 'registration.php';
        } elseif ($isRegionPage || $isCategoryPage) {
            $layout = 'list.php';
        } elseif ($this->adId && $this->ad && !$this->ad['deleted_time']) {
            $layout = 'ad.php';
        } elseif ($this->adId) {
            $this->addFlashMessage('The ad was deleted.');
            $this->redirect($this->generateCategoryUrl($this->category));
            $layout = '404.php';
        } else {
            $layout = '404.php';
        }

        return $layout;
    }

    public function addFlashMessage(string $message)
    {
        setcookie('flash_message', $message, time() + 60 * 10, '/');
    }

    public function getFlashMessage(): string
    {
        $message = $_COOKIE['flash_message'] ?? '';
        unset($_COOKIE['flash_message']);
        setcookie('flash_message', null, -1, '/');

        return $message;
    }

    public function getCategories(int $parentId, int $level = 0, int $limit = 0, $offset = 0, $orderBy = ''): array
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

        if ($orderBy) {
            $query .= ' ORDER BY ' .$orderBy;
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit OFFSET %d_offset';
            $values['limit'] = $limit;
            $values['offset'] = $offset;
        }

        return $this->getDb()->query($query, $values);
    }

    public function getAdsRegions(array $regionIds): array
    {
        if ($regionIds) {
            $regions = $this->getDb()->query('SELECT * FROM regions WHERE id IN %ld', $regionIds);
            $grouped = $regions ? $this->groupByField($regions, 'id') : [];
            $adsRegions = [];
            foreach ($regionIds as $regionId) {
                $adsRegions[$regionId] = isset($grouped[$regionId])
                    ? $grouped[$regionId][0]
                    : $this->getDefaultRegion();
            }

            return $adsRegions;
        }


        return [];
    }

    public function getAdRegion(?int $regionId): array
    {
        return $regionId
            ? $this->getDb()->queryFirstRow('SELECT * FROM regions WHERE id = %d', $regionId)
            : $this->getDefaultRegion();
    }

    public function getCategory(int $categoryId): array
    {
        return $this->getDb()->queryFirstRow('SELECT * FROM categories WHERE id = %d', $categoryId);
    }

    public function getRegions(int $parentId, int $level = 0, int $limit = 0, int $offset = 0, $orderBy = ''): array
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

        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if ($limit) {
            $query .= ' LIMIT %d_limit OFFSET %d_offset';
            $values['limit'] = $limit;
            $values['offset'] = $offset;
        }

        return $this->getDb()->query($query, $values);
    }

    public function getIP(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function getCurrentAd(): ?array
    {
        return $this->ad ?? null;
    }

    public function getLatitude(): string
    {
        return $this->getCoordinate(0);
    }

    public function getLongitute(): string
    {
        return $this->getCoordinate(1);
    }

    public function getAccuracy(): string
    {
        return $this->getCoordinate(2);
    }

    public function isAdUrlExists(string $url): bool
    {
        $found = $this->getDb()->queryFirstRow('SELECT * FROM ads WHERE url = %s', $url);

        return $found ? true : false;
    }

    public function findCategoryUrl(string $title, int $level): string
    {
        $urlPattern = $this->generateUrl($title);
        $url = $urlPattern;
        $counter = 0;
        while ($this->getUrlCategory($url, $level)) {
            $url = $urlPattern . '-' . (++$counter);
        }

        return $url;
    }

    public function findRegionUrl(string $title): string
    {
        $urlPattern = $this->generateUrl($title);
        $url = $urlPattern;
        $counter = 0;
        while ($this->getUrlRegion($url)) {
            $url = $urlPattern . '-' . (++$counter);
        }

        return $url;
    }

    public function upperCaseEveryWord(string $text): string
    {
        $words = explode(' ', $text);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }

        return implode(' ', $words);
    }

    public function generateUrl(string $text): string
    {
        $a = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', ' '];
        $b = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', '-'];

        return strtolower(str_replace($a, $b, $text));
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
            'SELECT id FROM details_fields WHERE category_id = %d AND field = %s LIMIT 1',
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
        return $this->getDb()->queryFirstField("SELECT MAX(create_time) FROM ads");
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
                    'url' => $this->pageNumber == $sliderPage
                        ? ''
                        : $this->getPageUrl($sliderPage),
                ];
            }

            if ($hasRightDots) {
                $urls[] = [
                    'title' => '...',
                    'url' => ''
                ];
                $urls[] = [
                    'title' => $this->pagesCount,
                    'url' => $this->pageNumber == $this->pagesCount
                        ? ''
                        : $this->getPageUrl($this->pagesCount),
                ];
            }
        }

        return $urls;
    }

    /**
     * @param int|array|null $categoryId
     * @param int|null $regionId
     * @return int
     */
    public function getAdsCount($categoryId, ?int $regionId): int
    {
        $query = 'SELECT COUNT(*) FROM ads AS a LEFT JOIN categories AS c ON a.category_id = c.id'
            . ' LEFT JOIN regions AS r ON a.region_id = r.id';
        $regionsIds = $this->getRegionsChildrenIds(array_filter([$regionId]));
        [$where, $values] = $this->getAdsWhere($categoryId, $regionsIds);
        $query .= $where;

        return $this->getDb()->queryFirstField($query, $values);
    }

    public function getAds($categoryId, $regionId, int $limit, int $offset = 0): array
    {
        $query = $this->getAdsQuery();
        $regionsIds = $this->getRegionsChildrenIds(array_filter([$regionId]));
        [$where, $values] = $this->getAdsWhere($categoryId, $regionsIds);
        $query .= $where;
        $query .= ' ORDER BY create_time DESC LIMIT %d_limit OFFSET %d_offset';
        $values['limit'] = $limit;
        $values['offset'] = $offset;
        $ads = $this->getDb()->query($query, $values);
        $extendedAds = $this->addAdsData($ads);

        return array_values($extendedAds);
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
            'tree_id' => 0,
            'level' => 0,
            'title' => '',
            'url' => '',
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

    public function hasNextPage(int $count): bool
    {
        return $count == $this->getAdsLimit();
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

    public function filterString(string $param): string
    {
        return $param ? trim(strip_tags(htmlentities($param))) : '';
    }

    public function getCategoryUrl(): string
    {
        return $this->categoryUrl;
    }

    public function generateAdUrl(array $ad): string
    {
        $category = $this->getCategory($ad['category_id']);
        $region = $this->getAdRegion($ad['region_id']);

        return $this->generateCategoryUrl($category, $region) . '/ad' . $ad['id'];
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
                    [$region['url'] ?? $this->getCurrentRegion()['url'] ?? $this->getDefaultRegion()['url']],
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

    public function sendEmail(string $toEmail, string $subject, string $body): int
    {
        $env = $this->getEnv();
        $transport = (new \Swift_SmtpTransport($env['SMTP_HOST'], $env['SMTP_PORT'], $env['SMTP_ENCRYPTION']))
            ->setUsername($env['SMTP_EMAIL'])
            ->setPassword($env['SMTP_PASSWORD']);
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message($subject))
            ->setContentType("text/html")
            ->setFrom([$env['SMTP_EMAIL'] => $env['SMTP_FROM']])
            ->setTo([$toEmail])
            ->setBody($body);

        return $mailer->send($message);
    }

    public function checkAuth()
    {
        if (empty($_SERVER['PHP_AUTH_USER'])) {
            $this->showAuthForm();
        } else {
            $login = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            if ($login != $this->getEnv()['AUTH_LOGIN']
                || $password != $this->getEnv()['AUTH_PASSWORD']
            ) {
                $this->showAuthForm();
            }
        }
    }

    public function calculatePagesCount(int $count): int
    {
        return max(ceil($count / $this->adsLimit), 1);
    }

    public function initPager(bool $hasNextPage): void
    {
        $this->initCurrentPage();
        $this->pagesCount = $hasNextPage ? $this->pageNumber + 1 : $this->pageNumber;
        $this->initPages();
    }

    public function initPagination(int $count): void
    {
        $this->initCurrentPage();
        $this->pagesCount = $this->calculatePagesCount($count);
        $this->initPages();
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

    private function getCategoriesUrls(): array
    {
        $parts = $this->getUrlParts();
        if (count($parts) >= 2) {
            $lastPart = $parts[count($parts) - 1] ?? '';
            if ($this->isPageUrlPart($lastPart) || $this->isAdUrlPart($lastPart)) {
                unset($parts[count($parts) - 1]);
            }

            unset($parts[0]);

            return array_values($parts);
        }

        return [];
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
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

    public function generateShortText(string $text, int $length = 135): string
    {
        $cleanText = strip_tags($text);
        $short = mb_substr($cleanText, 0, $length);
        if ($short != $cleanText) {
            $short .= '...';
        }

        return $short;
    }

    public function getAdBreadcrumbUrls(): array
    {
        $urls = [[
            'title' => $this->getDefaultRegion()['title'],
            'url' => $this->generateRegionUrl($this->getDefaultRegion()),
        ], [
            'title' => $this->getCurrentRegion()['title'] ?? $this->getCurrentRegion()['title'],
            'url' => $this->generateRegionUrl($this->getCurrentRegion()),
        ]];

        return array_merge(
            $urls,
            $this->getCategoryBreadcrumbUrls(
                array_merge($this->getCurrentCategory()['parents'], [$this->getCurrentCategory()]),
                $this->getCurrentRegion()
            )
        );
    }

    public function getListBreadcrumbUrls(): array
    {
        $urls = [[
            'title' => $this->getDefaultRegion()['title'],
            'url' => $this->generateRegionUrl($this->getDefaultRegion()),
        ]];
        if ($this->getCurrentRegion()['url'] != $this->getDefaultRegion()['url']) {
            $urls[] = [
                'title' => $this->getCurrentRegion()['title'] ?? $this->getCurrentRegion()['title'],
                'url' => $this->generateRegionUrl($this->getCurrentRegion()),
            ];
        }

        $urls = array_merge(
            $urls,
            $this->getCategoryBreadcrumbUrls($this->getCurrentCategory()['parents'], $this->getCurrentRegion())
        );
        if ($this->getCurrentCategory() && $this->getCurrentCategory()['title']) {
            $urls[] = [
                'title' => $this->getCurrentCategory()['title'],
                'url' => $this->generateCategoryUrl($this->getCurrentCategory(), $this->getCurrentRegion()),
            ];
        }
        foreach ($urls as &$url) {
            if ($url['url'] == $_SERVER['REQUEST_URI']) {
                $url['url'] = '';
            }
        }

        return $urls;
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

    public function safeTranslate(string $text, string $fromLanguageCode, string $toLanguageCode): string
    {
        if ($fromLanguageCode == $toLanguageCode || !$fromLanguageCode || !$toLanguageCode) {
            return $text;
        } elseif ($foundTranslate = $this->getDb()->queryFirstField(
            "SELECT to_text FROM translates WHERE from_code = %s AND to_code = %s AND from_text = %s",
            $fromLanguageCode,
            $toLanguageCode,
            $text
        )) {
            return $foundTranslate;
        } else {
            $translate = $this->translate($text, $fromLanguageCode, $toLanguageCode);
            if ($translate && $translate != $text) {
                $this->getDb()->insert(
                    'translates',
                    [
                        'from_code' => $fromLanguageCode,
                        'to_code' => $toLanguageCode,
                        'from_text' => $text,
                        'to_text' => $translate
                    ]
                );

                return $translate;
            }
        }

        return $text;
    }

    public function translate(string $text, string $fromLanguageCode, string $toLanguageCode): string
    {
        if ($text) {
            $response = $this->sendYandexRequest(
                'https://translate.api.cloud.yandex.net/translate/v2/translate',
                [
                    "sourceLanguageCode" => $fromLanguageCode,
                    "targetLanguageCode" => $toLanguageCode,
                    "format" => "PLAIN_TEXT",
                    "texts" => [$text]
                ]
            );
            if ($response) {
                $parsedResponse = json_decode($response);
                $text = $parsedResponse->translations[0]->text;
            }
        }

        return $text;
    }

    public function parseYoutubeVideoId(string $query): string
    {
        $html = PylesosService::get(
            'https://www.youtube.com/results?search_query=' . urlencode($query),
            [],
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

        return $videoId ?? '';
    }

    public function isDebug(): bool
    {
        return $this->getEnv()['DEBUG'] || ($_GET['debug'] ?? 0);
    }

    public function getListAdBreadcrumbUrls(array $ad): array
    {
        $category = $this->getCategory($ad['category_id']);
        $categories = $this->getParentCategories($category);
        $region = $this->getAdRegion($ad['region_id']);

        return $this->getCategoryBreadcrumbUrls(array_merge($categories, [$category]), $region);
    }

    public function getDefaultRegion(): array
    {
        return [
            'id' => 0,
            'tree_id' => 0,
            'level' => 0,
            'title' => $this->defaultRegionTitle,
            'url' => $this->defaultRegionUrl,
            'children' => [],
            'parents' => []
        ];
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

    private function getChildLevelCategoriesIds(array $categoriesIds, int $level): array
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

    private function initDefaultRegion()
    {
        $this->setDefaultRegionTitle($this->env['DEFAULT_REGION_TITLE']);
        $this->setDefaultRegionUrl($this->env['DEFAULT_REGION_URL']);
    }

    private function initRegion()
    {
        if ($this->regionUrl == $this->defaultRegionUrl || !$this->regionUrl) {
            $this->region = $this->getDefaultRegion();
        } else {
            $this->region = $this->db->queryFirstRow('SELECT * FROM regions WHERE url = %s', $this->regionUrl);
            if ($this->region) {
                $this->region['parents'] = $this->getParentsRegions($this->region);
            }
        }
    }

    private function initCategoriesUrls()
    {
        $this->categoriesUrls = $this->getCategoriesUrls();
        if ($this->categoriesUrls) {
            $this->categoryUrl = $this->categoriesUrls[count($this->categoriesUrls) - 1];
            $this->categoryLevel = count($this->categoriesUrls);
        }
    }

    private function initCategory()
    {
        if ($this->categoryUrl) {
            $this->category = $this->getUrlCategory($this->categoryUrl, $this->categoryLevel);
            if ($this->category) {
                $this->category['parents'] = $this->getParentCategories($this->category);
                $this->category['children'] = $this->getChildCategories($this->category);
            }
        }
    }

    private function sendYandexRequest(string $url, array $post)
    {
        $apiKey = $this->getEnv()['YANDEX_TRANSLATE_API_KEY'];
        $ch = curl_init($url);
        $post = json_encode($post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Api-Key ' . $apiKey]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection

        return $result;
    }

    private function initLogger()
    {
        $this->logger = new Logger('palto');
        $this->logger->pushHandler(new StreamHandler('php://stdout'));
        $this->logger->pushHandler(new RotatingFileHandler(
                                       $this->rootDirectory . '/logs/parser',
                                       20,
                                       Logger::INFO
                                   ));
    }

    private function addAdsData(array $ads): array
    {
        $adIds = array_column($ads, 'id');
        $images = $this->getAdsImages($adIds);
        $details = $this->getAdsDetails($adIds);
        $regions = $this->getAdsRegions(array_column($ads, 'region_id'));
        $grouped = [];
        foreach ($ads as $ad) {
            $ad['images'] = $images[$ad['id']] ?? [];
            $ad['details'] = $details[$ad['id']] ?? [];
            $ad['region'] = $regions[$ad['region_id']] ?? $this->getDefaultRegion();
            $grouped[$ad['id']] = $ad;
        }

        return $grouped;
    }

    private function addAdData(?array $ad): ?array
    {
        if ($ad) {
            $ad['images'] = $this->getAdsImages([$ad['id']])[$ad['id']] ?? [];
            $ad['details'] = $this->getAdsDetails([$ad['id']])[$ad['id']] ?? [];
            $ad['region'] = $this->getAdsRegions([$ad['region_id']])[$ad['region_id']];
        }

        return $ad;
    }

    private function getAdsDetails(array $adIds): array
    {
        if ($adIds) {
            $details = $this->getDb()->query(
                'SELECT ad_id, field, value FROM details_fields AS df INNER JOIN ads_details AS dfv ON df.id = dfv.details_field_id WHERE ad_id IN %ld',
                $adIds
            );
            $groupedByAdId = $this->groupByField($details, 'ad_id');
            $groupedWithDetails = [];
            foreach ($groupedByAdId as $adId => $adDetails) {
                $groupedWithDetails[$adId] = array_column(
                    $adDetails,
                    'value',
                    'field'
                );
            }

            return $groupedWithDetails;
        }

        return [];
    }

    private function getAdsImages(array $adIds): array
    {
        if ($adIds) {
            $images = $this->getDb()->query('SELECT ad_id, big, small FROM ads_images WHERE ad_id IN %ld', $adIds);

            return $this->groupByField($images, 'ad_id');
        }

        return [];
    }

    private function groupByField(array $unGrouped, string $field): array
    {
        $grouped = [];
        foreach ($unGrouped as $data) {
            if (!isset($data[$field])) {
                throw new \Exception('Undefined key ' . $field . ' in array: can not group');
            }

            $grouped[$data[$field]][] = $data;
        }

        return $grouped;
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

    /**
     * @param int|array|null $categoryId
     * @param int|array|null $regionId
     * @return array
     */
    private function getAdsWhere($categoryId, $regionId): array
    {
        $query = ' WHERE ';
        $values = [];
        $where = [];
        if ($categoryId || $regionId) {
            $values['category'] = $categoryId;
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
        }

        $where[] = 'a.deleted_time IS NULL';
        $query .= implode(' AND ', $where);

        return [$query, $values];
    }

    public function getParserPid(): int
    {
        return Status::getPhpCommandPid(self::PARSE_ADS_SCRIPT, $this->getProjectName());
    }

    private function isCron(): bool
    {
        return $this->isCli() && !isset($_SERVER['TERM']);
    }

    private function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    private function getParentsRegions(array $region): array
    {
        $parents = [];
        while ($region['parent_id'] ?? 0) {
            $region = $this->getAdRegion($region['parent_id']);
            $parents[] = $region;
        }

        return array_reverse($parents);
    }

    private function getUrlParts(): array
    {
        return array_values(array_filter(explode('/', parse_url($this->url)['path'])));
    }

    private function initDb(): void
    {
        $this->db = new \MeekroDB(
            '127.0.0.1',
            $this->env['DB_USER'],
            $this->env['DB_PASSWORD'],
            $this->env['DB_NAME'],
            3306,
            'utf8'
        );
        if ($this->isDebug() && !$this->isCli()) {
            $this->getDb()->debugMode();
        }

        $errorHandler = function ($params) {
            $this->getLogger()->error('Database error: ' . $params['error']);
            $this->getLogger()->error('Database query: ' . $params['query'] ?? '');
            throw new \Exception('Database error: ' . $params['error']);
        };
        $this->db->error_handler = $errorHandler; // runs on mysql query errors
        $this->db->nonsql_error_handler = $errorHandler; // runs on library errors (bad syntax, etc)
    }

    private function getUrlRegion(string $url): ?array
    {
        return $this->db->queryFirstRow('SELECT * FROM categories WHERE url = %s', $url);
    }

    private function getUrlCategory(string $url, int $level): ?array
    {
        return $this->db->queryFirstRow(
            'SELECT * FROM categories WHERE url = %s AND level = %d',
            $url,
            $level
        );
    }

    private function showAuthForm()
    {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo "Access denied!" . PHP_EOL;
        exit;
    }

    private function getCoordinate(int $partNumber): string
    {
        if ($this->ad && $this->ad['coordinates']) {
            $parts = explode(',', $this->ad['coordinates']);

            return $parts[$partNumber] ?? '';
        }

        return '';
    }

    private function redirect(string $categoryUrl)
    {
        header("Location: " . $categoryUrl,true,301);
    }

    private function initCurrentPage(): int
    {
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1] ?? '';
        $this->pageNumber = $this->isPageUrlPart($lastPart) ? intval($lastPart) : 1;

        return $this->pageNumber;
    }

    private function initPages()
    {
        if ($this->pageNumber + 1 <= $this->pagesCount) {
            $this->nextPageUrl = $this->getPageUrl($this->pageNumber + 1);
        }

        if ($this->pageNumber > 1) {
            $this->previousPageUrl = $this->getPageUrl($this->pageNumber - 1);
        }
    }

    public function getRegionsChildrenIds(array $parentIds, array $mergedIds = [])
    {
        if ($parentIds) {
            $childrenIds = $this->getDb()->queryFirstColumn('SELECT id FROM regions WHERE parent_id IN %ld', $parentIds);

            return array_merge($childrenIds, array_merge($mergedIds, $childrenIds));
        }

        return $mergedIds;
    }
}