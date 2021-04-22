<?php

namespace Palto;

use JetBrains\PhpStorm\Pure;

class Palto
{
    private string $title;

    private string $h1;

    private string $description;

    private string $previousPageUrl;

    private string $nextPageUrl = '';

    private \MeekroDB $db;
    private string $regionUrl = '';
    private string $categoryUrl = '';
    private int $adId = 0;
    private ?array $region;
    private ?array $category;
    private ?array $ad;

    private string $layoutDirectory = 'layouts/';

    private int $pageNumber = 1;

    public function __construct(private string $url, private array $env)
    {
        $this->initDb($env);
        $this->initRegionUrl();
        $this->initCategoryUrl();
        $this->initAdId();
        $this->initRegion();
        $this->initCategory();
        $this->initPageNumber();
        $this->initAd();
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

    public function loadLayout(string $layout)
    {
        require_once $this->layoutDirectory . $layout;
    }

    public function getRegion(): ?array
    {
        return $this->region;
    }

    private function initRegionUrl()
    {
        $parts = $this->getUrlParts();
        $this->regionUrl = $parts[1] ?? '';
    }

    private function initRegion()
    {
        if ($this->regionUrl) {
            $this->region = $this->db->queryFirstRow('SELECT * FROM regions WHERE url = %s', $this->regionUrl);
        }
    }

    private function initPageNumber()
    {
        $parts = $this->getUrlParts();
        $lastPart = $parts[count($parts) - 1];
        if ($this->isPageUrlPart($lastPart)) {
            $this->pageNumber = intval($lastPart);
        }
    }

    private function initCategoryUrl()
    {
        $parts = $this->getUrlParts();
        if (count($parts) > 2) {
            $lastPart = $parts[count($parts) - 1];
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

    public function getCategory(): ?array
    {
        return $this->category;
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
        $lastPart = $parts[count($parts) - 1];
        if ($this->isAdUrlPart($lastPart)) {
            $this->adId = intval(substr($lastPart, 2));
        }
    }

    private function initAd()
    {
        if ($this->adId) {
            $this->ad = $this->db->queryFirstRow('SELECT * FROM ads WHERE id = %s', $this->adId);
        }
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

    /**
     * @param array $env
     */
    private function initDb(array $env): void
    {
        $this->db = new \MeekroDB('localhost', $env['DB_USER'], $env['DB_PASSWORD'], $env['DB_NAME']);
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
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
    
    private function getUrlParts(): array
    {
        return array_values(array_filter(explode('/', $this->url)));
    }
}